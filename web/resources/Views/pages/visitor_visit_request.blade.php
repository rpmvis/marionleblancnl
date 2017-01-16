<?php
$src = $context['img_blueDot'];
$img_BlueDot = "<img class='img_BlueDot' src='$src'>";
?>

@extends('layouts.default')

@include('includes.tabmenu')

@section('content')
    <style>
       .max-width-column1 {  max-width: 250px ;}
    </style>

    <div>
        <script type="text/javascript" language="javascript"
                src='http://www.marionleblanc.nl/javascript/werk_marion.js'>
        </script>

        <script type="text/javascript" language="javascript">frmAanvraag_init();
        </script>
    </div>
    <div>
        <form action=''
              id='frmAanvraag'
              name='frmAanvraag'
              onsubmit="return validate_form()"
              method="post">
            <div class='kop3'>
                <pre>{!! $img_BlueDot !!}&nbsp;{{{ $values['table_header'] }}}</pre>
            </div>
            <p>Na verzending neemt Marion binnen 2 werkdagen contact met u op.</p>
            <p>Ja, ik zou op atelierbezoek willen op:</p>
            <div class='grid-row'>
                <div class='grid-item max-width-column1' width="100px" >
                    <label for='bezDatum'>Datum dd-mm-jjjj:<span class='paars'>*</span></label>
                </div>
                <div class='grid-item'>
                    <input id='bezDatum' name='bezDatum' type="text">
                </div>
            </div>
            <div class='grid-row'>
                <div class='grid-item max-width-column1'>
                    <label>Aanhef:</label>
                </div>
                <div class='grid-item'>
                    <select id='bezAanhef' name='bezAanhef'>
                        <option selected='selected' value=''></option>
                        <option value='De heer'>De heer</option>
                        <option value='Mevrouw'>Mevrouw</option>
                    </select>
                </div>
            </div>
            <div class='grid-row'>
                <div class='grid-item max-width-column1'>
                    <label for='bezNaam'>Naam:<span class='paars'>*</span></label>
                </div>
                <div class='grid-item'>
                    <input id='bezNaam' name='bezNaam' type="text">
                </div>
            </div>
            <div class='grid-row'>
                <div class='grid-item max-width-column1'>
                    <label for='bezTelefoon'>Telefoon:<span class='paars'>*</span></label>
                </div>
                <div class='grid-item'>
                    <input id='bezTelefoon' name='bezTelefoon' type="text">
                </div>
            </div>
            <div class='grid-row'>
                <div class='grid-item max-width-column1'>
                    <label for='bezBelVanaf'>Bel mij s.v.p tussen:</label>
                </div>
                <div class='grid-item'>
                    <input id='bezBelVanaf' name='bezBelVanaf' type="text">
                </div>
            </div>
            <div class='grid-row'>
                <div class='grid-item max-width-column1'>
                    <label for='bezBelTot'>en:</label>
                </div>
                <div class='grid-item'>
                    <input id='bezBelTot' name='bezBelTot' type="text">
                </div>
            </div>
            <div class='grid-row'>
                <div class='grid-item max-width-column1'>
                    <label for='bezEmail'>Email adres:<span class='paars'>*</span></label>
                </div>
                <div class='grid-item'>
                    <input id='bezEmail' name='bezEmail' type="text">
                </div>
            </div>
            <div class='grid-row'>
                <div class='grid-item max-width-column1'>
                    <label for='bezOpmerking'>Opmerking:</label>
                </div>
                <div class='grid-item'>
                    <textarea id="bezOpmerking" name='bezOpmerking' rows="2" cols="40">
                </textarea>
                </div>
            </div>
            <div class='grid-row'>
                <div class='grid-item max-width-column1'>
                    <p><span class='paars'>*</span>verplicht veld</>
                </div>
                <div class='grid-item'>
                    <input id='submit' name='submit' type='submit' value='Verzenden'
                           onclick='return validate_form()'>
                    &nbsp;&nbsp;
                    <input id='reset' name='reset' type='reset' value='Herstel'>
                </div>
            </div>
        </form>
    </div>
@endsection

