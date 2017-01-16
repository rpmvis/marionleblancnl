<?php

namespace Studio\Controllers\Helpers;

use app\MyApplication;
use Symfony\Component\Validator\Constraints as Assert;

class Visitor
{
    protected $app;
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

    public function __construct(MyApplication $app, array $context){
        $this->app = $app;
        $this->context = $context;
        $this->setVisitor($_POST);
    }

    private function setVisitor(array $prms):Visitor{
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

    public function validate():string{
        $errors = $this->app['validator']->validate($this);
        $errmsg = null;
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errmsg .= $error->getPropertyPath().' '.$error->getMessage()."\n";
            }
        }
        return $errmsg;
    }

    function visitor_visit_confirmed_html(){
        $viewname = 'pages.visitor_visit_confirmed_html';
        $data = array('visitor' => $this);
        $view = $this->app['blade']->view($viewname, $data);
        return $view;
    }

    function visitor_visit_confirmed_thanks_html(){
        $viewname = 'pages.visitor_visit_confirmed_thanks_html';
        $data = array('context' => $this->context, 'visitor' => $this);
        $view = $this->app['blade']->view($viewname, $data);
        return $view;
    }

    function visitor_visit_confirmed_plain(){
        $viewname = 'pages.visitor_visit_confirmed_plain';
        $data = array('visitor' => $this);
        $view = $this->app['blade']->view($viewname, $data);
        return $view;
    }

    function artist_visit_confirmed_html(){
        $viewname = 'pages.artist_visit_confirmed_html';
        $data = array('visitor' => $this);
        $view = $this->app['blade']->view($viewname, $data);
        return $view;
    }

    function artist_visit_confirmed_plain(){
        $viewname = 'pages.artist_visit_confirmed_plain';
        $data = array('visitor' => $this);
        $view = $this->app['blade']->view($viewname, $data);
        return $view;
    }
}
