<?php

namespace app\Services;

use Pimple\ServiceProviderInterface;

class EmailHelper implements ServiceProviderInterface
{
    protected $msg;
    private $mustHaveFrom = true;
    private $mustHaveSubject = true;
    /**
     * @var string
     */
    public function register(\Pimple\Container $app)
    {
        $app['email_validator'] = function() : EmailHelper {
            return $this;
        };
    }

    public function __construct(\Swift_Message $msg)
    {
        $this->msg = $msg;
    }

    function getMsg() : \Swift_Message {
        return $this->msg;
    }

    function setMustHaveFrom(bool $mustHaveFrom){
        $this->mustHaveFrom = $mustHaveFrom;
    }

    function setMustHaveSubject(bool $mustHaveSubject){
        $this->mustHaveSubject = $mustHaveSubject;
    }

    public function validate()
    {
        // validate $msg getTo email addresses
        if ($this->msg->getTo() == null)
            throw new \Exception("a 'To' email address was not provided.");

        foreach ($this->msg->getTo() as $to=>$value) {
            if (!$this->validate_email_address($to)){
                throw new \Exception("$to is not a valid 'To' email address");
            }
        }

        if ($this->mustHaveFrom == true){
            if ($this->msg->getFrom() == null)
                throw new \Exception("a 'From' email address was not provided; a From address is a must have.");

            foreach ($this->msg->getFrom() as $from=>$value) {
                if (!$this->validate_email_address($from)){
                    throw new \Exception("$from is not a valid 'From' email address.");
                }
            }
        }

    }

    private function validate_email_address($emailAddress){
        return filter_var($emailAddress, FILTER_VALIDATE_EMAIL);
    }
}