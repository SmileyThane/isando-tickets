<?php


namespace App\Repository;


use App\Company;
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
        try {
            $oClient = new IMAPClient();
            $oClient->connect();
            $aFolder = $oClient->getFolder('INBOX');
            $mailCache = MailCache::latest()->first();
            $since = $mailCache ? $mailCache->created_at : Carbon::now()->subDays(2);
            $messages = $aFolder->query()->since($since)->get();
//            dd($messages);
            $this->handleEmailMessages($messages, $type);
            return $this->success();
        } catch (\Throwable $th) {
            return $th;
        }
    }

    private function handleEmailMessages($messages, $type)
    {
        $responseBody = null;
        $i = 1;
        foreach ($messages as $key => $message) {
            Log::info($i++ . ') ');
            $res[$key]['sender'] = $message->getSender();
            $res[$key]['subject'] = $message->getSubject();
            Log::info('_________body_start' . $message->getTextBody() . '_________body_end');
            $senderObj = $res[$key]['sender'][0];
            $senderEmail = $senderObj->mail;
            CustomDbConnection::reset();
            $userGlobal = User::where('email', $senderEmail)->first();
            if ($userGlobal) {
                try {
                    $ticketAttributes['Subject'] = trim(str_replace("Re:", "", $res[$key]['subject']));
                    $ticketAttributes['Domain'] = $userGlobal->domain_hash;
                    $cachedCount = MailCache::where('message_key', $key)->count();
                    if ($cachedCount === 0) {
                        EmailLogRepository::addEmailCache($key, $res[$key]['subject']);
                    }
                    CustomDbConnection::connect($ticketAttributes['Domain']);
                    $ticket = Ticket::where('subject', 'like', $ticketAttributes['Subject'])->first();

                    if ($ticket !== null && $cachedCount === 0) {
                        $responseBody = $this->ticketAnswerFromEmail(
                            $senderEmail,
                            $ticketAttributes['Domain'],
                            $ticket,
                            $message,
                            $type
                        );
                    } elseif ($ticket === null && $cachedCount === 0) {
                        $responseBody = $this->createTicketFromEmail(
                            $senderEmail,
                            $ticketAttributes['Domain'],
                            $message,
                            $ticketAttributes['Subject']
                        );
                    }
                } catch (\Throwable $th) {
                    Log::info('connection was broken' . $th);
                }
            }
        }
        return $responseBody;
    }

    private function ticketAnswerFromEmail($senderEmail, $ticketDomain, $ticket, $message, $ticketType)
    {
        Log::info('answer');
        $user = User::where('email', $senderEmail)->first();
        if (!$user) {
            Log::info($senderEmail . ' not found');
        }
        $token = $user->createToken();
        $uri = '/api/' . $ticketDomain . '/ticket-' . $ticketType;
        $params = [
            'user_id' => $user->id,
            'ticket_id' => $ticket->ticket_id,
            'answer' => $message->getTextBody()
        ];
        return $this->makeSystemRequest($uri, $token, $params);
    }

    private function createTicketFromEmail($senderEmail, $ticketDomain, $message, $ticketSubject)
    {
        $type = 'create';
        $uri = '/api/' . $ticketDomain . '/ticket-' . $type;
        $userFrom = User::where('email', $senderEmail)->first();
        $userTo = User::where('domain_hash', '=', $ticketDomain)->first();

        if (!$userFrom) {
            Log::info($senderEmail . ' not found');
        }
        $token = $user->createToken();
        $companyFrom = Company::find($userFrom->userCompany()->company_id);
        $companyTo = Company::find($userTo->userCompany()->company_id);
        Log::info($companyFrom->name . '  === ' . $companyTo->name);
        $params = [
            'company_id' => $companyFrom->id,
            'created_by_company_user_id' => $userFrom->userCompany()->id,
            'message_subject' => $ticketSubject,
            'project_description' => $message->getTextBody(),
            'assigned_to_company_id' => $companyTo->id,
        ];
        return $this->makeSystemRequest($uri, $token, $params);
    }

    private function makeSystemRequest($uri, $token, $params)
    {
        $request = Request::create($uri, 'POST', $params);
        $request->headers->set('Authorization', 'Bearer ' . $token);
        $request->headers->set('Accept', 'application/json');
        $response = app()->handle($request);
        return $response->getContent();
    }



}
