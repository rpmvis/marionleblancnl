<?php

namespace app\Services;

class EmailService implements EmailServiceInterface{
    protected $mailer;
    protected $emailHelper;

    function __construct(\Swift_Mailer $mailer, EmailHelper $emailHelper)
    {
        $this->mailer = $mailer;
        $this->emailHelper = $emailHelper;
    }

    public function send($callback) : int{
        // fill emailHelper properties with callback
        call_user_func($callback, $this->emailHelper);

        $this->emailHelper->validate();

        $msg = $this->emailHelper->getMsg();

        // actual sending of email at this point
        $successful_recipients = $this->mailer->send($msg);
        return $successful_recipients;
    }
}
