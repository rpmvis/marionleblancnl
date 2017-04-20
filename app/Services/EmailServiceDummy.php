<?php

namespace app\Services;

class EmailServiceDummy implements EmailServiceInterface{
    protected $emailHelper;

    function __construct(EmailHelper $emailHelper)
    {
        $this->emailHelper = $emailHelper;
    }

    public function send($callback) : int{
        // stub method

        // fill emailHelper properties with callback
        call_user_func($callback, $this->emailHelper);

        $this->emailHelper->validate();

        // sending email is faked at this point
        $successful_recipients =1;
        return $successful_recipients;
    }
}
