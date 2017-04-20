<?php

namespace Studio\Models;

use Aea\Model\BladeProxy;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use app\Helpers\Helper;

class Visitor implements ServiceProviderInterface{

    protected $context;

    /**
     * @Assert\Date(message="De datum '{{ value }}' is niet geldig(dd/mm/jj)." )
     */
    public $bezDatum;

    /**
      * @Assert\Regex("/^De heer|Mevrouw||/",
     *     message="De aanhef moet 'De heer' of 'Mevrouw' of leeg zijn.")
     */
    public $bezAanhef;
    public $bezAanhef2;

    /**
     * @Assert\Regex("^[A-Z0-9-/\\s]{2,}$",
     *     message="Uw naam '{{ value }}' is niet geldig. Vuk een naam met met minimaal 2 letters.")
     */
    public $bezNaam;

    /**
     * @Assert\Regex("^[0-9-/\\s]{10,}$",
     *     message="Uw telefoonnummer '{{ value }}' is niet geldig (bijv. 010-1234567 of 0101234567 of 06-12345678 of 0612345678")
     */
    public $bezTelefoon;
    public $bezBelVanaf;
    public $bezBelTot;

    /**
     * @Assert\Email(
     *     message = "Email '{{ value }}' is niet geldig.",
     *     checkMX = true
     * )
     */
    public $bezEmail;
    public $bezOpmerking;

    protected $helper;
    protected $validator;
    protected $blade;

    public function __construct(Helper $helper, RecursiveValidator $validator, BladeProxy $blade){
        $this->validator = $validator;
        $this->blade = $blade;
        $this->helper = $helper;
    }

    public function register(\Pimple\Container $app){
        $app['visitor'] = function () {
            return $this;
        };
    }

    public function setVisitor(array $prms):Visitor{
        $this->bezDatum = strip_tags($prms['bezDatum']);

        $this->bezAanhef = strip_tags($prms['bezAanhef']);
        $s = $this->bezAanhef;
        if ($s === null){
            $s = heer/mevrouw;
        } else {
            $s = strtolower($s);
            // de heer --> heer
            if (substr($s, 0, 3) === 'de ') {
                $s = substr($s, 3);
            }

        }
        $this->bezAanhef2 = $s;
        $this->bezNaam = strip_tags($prms['bezNaam']);
        $this->bezTelefoon = strip_tags($prms['bezTelefoon']);
        $this->bezBelVanaf = strip_tags($prms['bezBelVanaf']);
        $this->bezBelTot = strip_tags($prms['bezBelTot']);
        $this->bezEmail = strip_tags($prms['bezEmail']);
        $this->bezOpmerking = strip_tags($prms['bezOpmerking']);

        return $this;
    }

    public function getVisitor(){
        return $this;
    }

    public function validate():string{
        $errors = $this->validator->validate($this);
        $errmsg = '';
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errmsg .= $error->getPropertyPath().' '.$error->getMessage()."\n";
            }
        }
        return $errmsg;
    }
}
