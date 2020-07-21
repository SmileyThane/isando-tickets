<?php


namespace App\Repository;

use App\Client;
use App\ClientCompanyUser;
use App\Company;
use App\CompanyProduct;
use App\MailCache;
use App\ProductClient;
use App\Role;
use App\Ticket;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Client as IMAPClient;

class EmailReceiverRepository
{
    public function receiveMail($type = 'answer')
    {
        Log::info('mail receiving process was started.');
        try {
            $oClient = new IMAPClient();
            $oClient->connect();
            Log::info('IMAP cennected.');
            $aFolder = $oClient->getFolder('INBOX');
            $mailCache = MailCache::latest()->first();
            Log::info('mail cache checked.');
            $since = $mailCache ? $mailCache->created_at : Carbon::now()->subDays(2);
            $messages = $aFolder->query()->since($since)->get();
            Log::info('messages are requested.');
            $this->handleEmailMessages($messages, $type);
            Log::info('mail receiving process was finished.');
            return ['success' => true];
        } catch (\Throwable $th) {
            Log::error($th);
            return $th;
        }
    }

    private function handleEmailMessages($messages, $type)
    {
        $responseBody = null;
        $i = 1;
        foreach ($messages as $key => $message) {
//            Log::info($i++ . ') ');
            $res[$key]['sender'] = $message->getSender();
            $res[$key]['subject'] = $message->getSubject();
//            Log::info('_________body_start' . $message->getTextBody() . '_________body_end');
            $senderObj = $res[$key]['sender'][0];
            $senderEmail = $senderObj->mail;
            $userGlobal = User::where('email', $senderEmail)->first();
            if ($userGlobal) {
                Log::info('email from ' . $userGlobal->name);
                try {
                    $ticketAttributes['Subject'] = trim(str_replace("Re:", "", $res[$key]['subject']));
                    $cachedCount = MailCache::where('message_key', $key)->count();
                    if ($cachedCount === 0) {
                        $this->addMailCache($key, $res[$key]['subject']);
                    }
                    $ticket = Ticket::where('name', 'like', $ticketAttributes['Subject'])
                        ->where(static function ($query) use ($userGlobal) {
                            return $query->where('from_company_user_id', $userGlobal->employee->id)
                                ->orWhere('to_company_user_id', $userGlobal->employee->id);
                        })->first();

                    if ($ticket !== null && $cachedCount === 0) {
                        Log::info('system starts creating answer for ticket ' . $ticket->id);
                        $responseBody = $this->ticketAnswerFromEmail($senderEmail, $ticket, $message);
                    } elseif ($ticket === null && $cachedCount === 0) {
                        Log::info('system starts creating new ticket');
                        $responseBody = $this->createTicketFromEmail($senderEmail, $message, $ticketAttributes['Subject']);
                    }
                } catch (\Throwable $th) {
                    Log::info('connection was broken' . $th);
                }
            }
        }
        return $responseBody;
    }

    private function ticketAnswerFromEmail($senderEmail, $ticket, $message)
    {
        $user = User::where('email', $senderEmail)->first();
        if (!$user) {
            Log::info($senderEmail . ' not found');
        }
        $token = $user->createToken('web');
        $uri = '/api/ticket/' . $ticket->id . '/answer';
        $params = [
            'company_user_id' => $user->employee->id,
            'ticket_id' => $ticket->id,
            'answer' => $this->removeEmptyParagraphs($message->getHTMLBody(true))
        ];
        Log::info('answer created');
        return $this->makeSystemRequest($uri, $token, $params);
    }

    private function createTicketFromEmail($senderEmail, $message, $ticketSubject)
    {
        $fromEntityId = $fromEntityType = $toEntityId = $toEntityType = $productId = null;
        $type = 'create';
        $uri = '/api/ticket';
        $userFrom = User::where('email', $senderEmail)->first();
        if ($userFrom->employee->hasRole(Role::COMPANY_CLIENT)) {
            $clientCompanyUser = ClientCompanyUser::where('company_user_id', $userFrom->employee->id)->first();
            if ($clientCompanyUser) {
                $fromEntityId = $clientCompanyUser->client_id;
                $fromEntityType = Client::class;
                $toEntityId = $userFrom->employee->company_id;
                $toEntityType = Company::class;
                $productClient = ProductClient::where('client_id', $fromEntityId)->first();
//                Log::alert($productClient);
                $productId = $productClient ? $productClient->product_id : null;
            }
        } else {
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
            $token = $userFrom->createToken('web');
//            Log::alert(json_encode($token));
            $params = [
                'from_entity_id' => $fromEntityId,
                'from_entity_type' => $fromEntityType,
                'to_entity_id' => $toEntityId,
                'to_entity_type' => $toEntityType,
                'to_product_id' => $productId,
                'from_company_user_id' => $userFrom->employee->id,
                'priority_id' => 2,
                'name' => $ticketSubject,
                'description' => $this->removeEmptyParagraphs($message->getHTMLBody(true)),
            ];
//            Log::alert($message->getHTMLBody(true));
            return $this->makeSystemRequest($uri, $token, $params);
        }
        Log::alert("check incorrect params fromEntityId=$fromEntityId  fromEntityType=$fromEntityType toEntityId=$toEntityId toEntityType=$toEntityType productId=$productId");
        return null;
    }

    private function makeSystemRequest($uri, $token, $params)
    {
        $request = Request::create($uri, 'POST', $params);
        $request->headers->set('Authorization', 'Bearer ' . $token->accessToken);
        $request->headers->set('Accept', 'application/json');
        $response = app()->handle($request);
        return $response->getContent();
    }

    public function addMailCache($key, $subject): MailCache
    {
        $mailCache = new MailCache();
        $mailCache->message_key = $key;
        $mailCache->subject = $subject;
        $mailCache->save();
        return $mailCache;
    }

    private function removeEmptyParagraphs($content)
    {
        $emptyLinesArray = ["<p><br /></p>", "<p><br/></p>", "<p></p>"];
        return str_replace($emptyLinesArray, "", $content);
    }


}
