<?php


namespace App\Repositories;

use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyProduct;
use App\Email;
use App\Http\Controllers\Controller;
use App\MailCache;
use App\Permission;
use App\ProductClient;
use App\Ticket;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Webklex\IMAP\Facades\Client as IMAPClient;

class EmailReceiverRepository
{
    protected $ticketRepo;

    const YAHOO_QUOTED_TAG_PART_FOR_REMOVING = '<div id="yahoo_quoted';
    const CUSTOM_QUOTED_TAG_PART_FOR_REMOVING = '<br id="lineBreakAtBeginningOfMessage">';

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepo = $ticketRepository;
    }

    public function receiveMail($type = 'answer')
    {
        Log::info('mail receiving process was started.');
        try {
            $oClient = IMAPClient::account('default');
            $oClient->connect();
            Log::info('IMAP cennected.');
            $aFolder = $oClient->getFolder('INBOX');
            $mailCache = MailCache::latest()->first();
            Log::info('mail cache checked.');
            Log::info('mail cache last connection at: ' . $mailCache->created_at);
            $since = $mailCache ? $mailCache->created_at : Carbon::now()->subDays(10);
            $messages = $aFolder->query()->since($since)->get();
            Log::info('messages are requested.');
            Log::info('count: ' . count($messages));
            $this->handleEmailMessages($messages, $type);
            Log::info('mail receiving process was finished.');
            return ['success' => true];
        } catch (Throwable $th) {
            Log::error($th);
            return $th;
        }
    }

    private function handleEmailMessages($messages, $type)
    {
        $responseBody = null;
        foreach ($messages as $key => $message) {

            if ($message->getFrom()) {
                $senderObject = $message->getFrom()[0];
                $senderEmail = $senderObject->mail;
            } else {
                continue;
            }

            $rawSubject = $message->getSubject();
            $userGlobal = null;
            $email = Email::where(['email' => $senderEmail, 'entity_type' => User::class])->first();
            if ($email) {
                $userGlobal = User::where(['id' => $email->entity_id])->first();
            }
            if ($userGlobal) {
                Log::info('email from ' . $userGlobal->name);
                try {
                    $ticketSubject = trim(str_replace(["Re:", "Fwd:"], "", $rawSubject));
                    Log::info("subject is $ticketSubject");
                    $cachedCount = MailCache::where('message_key', $key)->count();
                    if ($cachedCount === 0) {
                        $this->addMailCache($key, $rawSubject);
                    }
                    $ticket = Ticket::where('name', 'like', $ticketSubject)
                        ->where(static function ($query) use ($userGlobal) {
                            return $query->where('from_company_user_id', $userGlobal->employee->id)
                                ->orWhere('to_company_user_id', $userGlobal->employee->id)
                                ->orWhere('contact_company_user_id', $userGlobal->employee->id);
                        })->first();
                    $attachments = $this->handleEmailAttachments($message->getAttachments());
                    Log::info('empty ticket ' . (string)$ticket === null);
                    Log::info('cached count ' . (string)$cachedCount);
                    if ($ticket !== null && $cachedCount === 0 &&
                        !in_array($senderEmail, config('mail.contact_form_addresses'), true)
                    ) {
                        Log::info('system starts creating answer for ticket ' . $ticket->id);
                        $responseBody = $this->ticketAnswerFromEmail($senderEmail, $ticket, $message, $attachments);
                    } elseif ($ticket === null && $cachedCount === 0) {
                        Log::info('system starts creating new ticket');
                        $responseBody = $this->createTicketFromEmail($senderEmail, $message, $ticketSubject, $attachments);
                    }
                } catch (Throwable $th) {
                    Log::info('connection was broken' . $th);
                }
            } elseif (in_array($senderEmail, config('mail.contact_form_addresses'), true)) {
                $ticketSubject = trim(str_replace(["Re:", "Fwd:"], "", $rawSubject));
                Log::info("subject is $ticketSubject");
                $cachedCount = MailCache::where('message_key', $key)->count();
                if ($cachedCount === 0) {
                    $this->addMailCache($key, $rawSubject);
                    Log::info('system starts creating new ticket');
                    $msg = $message->getTextBody();
                    $paramsArray = $this->parseAutogeneratedMessage($msg);
                    if ($paramsArray) {
                        $generatedMessage = $this->createDescriptionFromParsedArray($paramsArray);
                        $senderEmail = $paramsArray['Email'];
                        $attachments = $this->handleEmailAttachments($message->getAttachments());
                        $priorityId = $this->parsePriorityByName($paramsArray['Ticket Escalation']);
                        $responseBody = $this->createTicketFromEmail($senderEmail, $generatedMessage['description'], $ticketSubject, $attachments, $priorityId, $generatedMessage['access_details']);
                    }
                }
            }
        }
        return $responseBody;
    }

    public function addMailCache($key, $subject): MailCache
    {
        $mailCache = new MailCache();
        $mailCache->message_key = $key;
        $mailCache->subject = $subject;
        $mailCache->save();
        return $mailCache;
    }

    private function handleEmailAttachments($attachments): array
    {
        $result = [];
        foreach ($attachments as $attachment) {
            $rand = Controller::getRandomString();
            $name = $attachment->getName();
            $path = "files/original/$rand/$name";
            Storage::put("/public/$path", $attachment->getContent());
            $result[] = $path;
        }
        return $result;
    }

    private function ticketAnswerFromEmail($senderEmail, $ticket, $message, $files = []): bool
    {
        $user = null;
        $email = Email::where(['email' => $senderEmail, 'entity_type' => User::class])->first();
        if ($email) {
            $user = User::where(['id' => $email->entity_id])->first();
        }
        if (!$user) {
            Log::info($senderEmail . ' not found');
        }
        $params = new Request();
        $params['answer'] = $this->prettifyAnswer($message);
        $params['files'] = $files;
        Log::info('answer created');
        return $this->ticketRepo->addAnswer($params, $ticket->id, $user->employee->id, false);
    }

    private function removeEmptyParagraphs($content)
    {
        $emptyLinesArray = ["<p><br /></p>", "<p><br/></p>", "<p></p>"];
        return str_replace($emptyLinesArray, "", $content);
    }

    private function createTicketFromEmail($senderEmail, $message, $ticketSubject, $files = [], $priorityId = 2, $accessDetails = null, $connectionDetails = null): ?Ticket
    {
        $fromEntityId = $fromEntityType = $toEntityId = $toEntityType = $productId = null;
        $userFrom = null;
        $email = Email::where(['email' => $senderEmail, 'entity_type' => User::class])->first();
        if ($email) {
            $userFrom = User::where(['id' => $email->entity_id])->first();
        }
        if ($userFrom &&
            (count($userFrom->employee->roles) === 0
                || $userFrom->employee->hasPermissionId(Permission::EMPLOYEE_CLIENT_ACCESS)
            )
        ) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', $userFrom->employee->id)->first();
            if ($clientCompanyUser) {
                $fromEntityId = $clientCompanyUser->client_id;
                $fromEntityType = Client::class;
                $toEntityId = $userFrom->employee->company_id;
                $toEntityType = Company::class;
                $productClient = ProductClient::where('client_id', $fromEntityId)->first();
                $productId = $productClient ? $productClient->product_id : null;
            }
        } elseif ($userFrom) {
            $fromEntityId = $userFrom->employee->company_id;
            $fromEntityType = Company::class;
            $toEntityId = $userFrom->employee->company_id;
            $toEntityType = Company::class;
            $companyProduct = CompanyProduct::where('company_id', $fromEntityId)->first();
            $productId = $companyProduct ? $companyProduct->product_id : null;
        }
        if (!$userFrom) {
            Log::info($senderEmail . ' not found');
        }
        if ($fromEntityId && $fromEntityType && $toEntityId && $toEntityType && $productId) {
            $params = new Request();
            $params['from_entity_id'] = $fromEntityId;
            $params['from_entity_type'] = $fromEntityType;
            $params['to_entity_id'] = $toEntityId;
            $params['to_entity_type'] = $toEntityType;
            $params['to_product_id'] = $productId;
            $params['priority_id'] = $priorityId;
            $params['name'] = $ticketSubject;
            $params['contact_company_user_id'] = $userFrom->employee->id;
            $params['access_details'] = $accessDetails;
            $params['connection_details'] = $connectionDetails;
            if (is_string($message)) {
                $params['description'] = $message;
            } else {
                $params['description'] = $this->prettifyAnswer($message);
            }
            $params['files'] = $files;
            return $this->ticketRepo->create($params, $userFrom->employee->id);
        }
        Log::alert("check incorrect params fromEntityId=$fromEntityId  fromEntityType=$fromEntityType toEntityId=$toEntityId toEntityType=$toEntityType productId=$productId");
        return null;
    }

    private function parseAutogeneratedMessage($message): array
    {
        $prettifiedString = preg_replace(
            "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/",
            "\n",
            str_replace(":\r\n", ': ', $message)
        );
        $rawArray = explode("\n", $prettifiedString);
        $parsedArray = [];
        $key = 0;
        foreach ($rawArray as $rawItem) {
            $parsedItem = explode(': ', $rawItem);
            if (count($parsedItem) > 1) {
                $key = trim($parsedItem[0]);
                if ($key === 'Email') {
                    $parsedItem[1] = str_replace(['<', '>'], '', $parsedItem[1]);
                }
                $parsedArray[$key] = trim($parsedItem[1]);
            } else {
                $parsedArray[$key] .= "<br>" . trim($parsedItem[0]);
            }
        }
        return $parsedArray;
    }

    private function createDescriptionFromParsedArray($parsedArray): array
    {
        $message = [];
        unset($parsedArray['Email'], $parsedArray['Ticket Escalation']);
        $message['description'] = $message['access_details'] = '';
        $message['description'] .= '<p><strong>From: </strong>' . $parsedArray['From / Name'] . ' - ' . $parsedArray['Firm / Organisation'] . ' sent on ' . now()->format('d-m-Y h:i:s') . "</p>";
        $message['description'] .= '<p><strong>Phone: </strong>' . $parsedArray['Phone'] . "</p>";
        $message['description'] .= '<p><strong>Mobile: </strong>' . $parsedArray['Mobile'] . "</p>";
        $message['description'] .= '<p><strong>Briefly describe your problem: </strong>' . str_replace("\n", '<br/>', $parsedArray['Briefly describe your problem']) . "</p>";

        $message['access_details'] .= 'Software/Hardware affected: ' . $parsedArray['Software/Hardware affected'] . "<br>";
        $message['access_details'] .= 'Which Version: ' . $parsedArray['Which Version'] . "<br>";
        $message['access_details'] .= 'Last Update: ' . $parsedArray['Last Update'] . "<br>";
        return $message;
    }

    private function parsePriorityByName($priorityName = ''): int
    {
        $priorityId = 2;
        $prioritiesArray = [
            'dringend' => 1,
            'urgent' => 1,
            'moderate' => 2,
            'moderat' => 2,
            'demnächst' => 3,
            'someday' => 3
        ];
        foreach ($prioritiesArray as $key => $value) {
            if (strtolower($priorityName) === $key) {
                $priorityId = $value;
            }
        }
        return $priorityId;
    }

    private function prettifyAnswer($message)
    {
        $result = $message->hasHTMLBody() ? $this->removeEmptyParagraphs($message->getHTMLBody(true)) : $message->getTextBody();

        if (strstr($result, self::YAHOO_QUOTED_TAG_PART_FOR_REMOVING)) {
            $result = explode(self::YAHOO_QUOTED_TAG_PART, $result)[0];
        }

        if (strstr($result, self::CUSTOM_QUOTED_TAG_PART_FOR_REMOVING)) {
            $result = explode(self::CUSTOM_QUOTED_TAG_PART_FOR_REMOVING, $result)[0];
        }

        return $result;
    }

    private function makeSystemRequest($uri, $token, $params)
    {
        $request = Request::create($uri, 'POST', $params);
        $request->headers->set('Authorization', 'Bearer ' . $token->accessToken);
        $request->headers->set('Accept', 'application/json');
        $response = app()->handle($request);
        return $response->getContent();
    }

}
