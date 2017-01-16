<style>
    .max-width-column1 {  max-width: 250px ;}
</style>

<p>Geachte {!! $visitor->bezAanhef2 !!}&nbsp;{!! $visitor->bezNaam !!}<br><br>
    Dank u voor het verzenden van uw aanvraag.<br>
    Ik neem uiterlijk binnen 2 werkdagen contact met u op om een afspraak te maken over een atelierbezoek.<br><br>
    Hieronder vindt u nog een overzicht van de data die u naar mij verzonden hebt.<br><br>
    Met vriendelijke groet,<br>Marion Le Blanc
    <br><br>

<b>Overzicht verzonden data</b>
<br>

<div class="grid-row">
    <div class="grid-item max-width-column1">Datum:</div>
    <div class="grid-item">{!! $visitor->bezDatum !!}</div>
</div>
<div class="grid-row">
    <div class="grid-item max-width-column1">Aanhef:</div>
    <div class="grid-item">{!! $visitor->bezAanhef !!}</div>
 </div>
<div class="grid-row">
    <div class="grid-item max-width-column1">Naam:</div>
    <div class="grid-item">{!! $visitor->bezNaam !!}</div>
</div>
<div class="grid-row">
    <div class="grid-item max-width-column1">Telefoon:</div>
    <div class="grid-item">{!! $visitor->bezTelefoon !!}</div>
</div>
<div class="grid-row">
    <div class="grid-item max-width-column1">Bel mij s.v.p tussen:</div>
    <div class="grid-item">{!! $visitor->bezBelVanaf !!}</div>
</div>
<div class="grid-row">
     <div class="grid-item max-width-column1">en:</div>
     <div class="grid-item">{!! $visitor->bezBelTot !!}</div>
</div>
<div class="grid-row">
    <div class="grid-item max-width-column1">Email adres:</div>
    <div class="grid-item">{!! $visitor->bezEmail !!}</div>
</div>
<div class="grid-row">
    <div class="grid-item max-width-column1">Opmerking:</div>
    <div class="grid-item">{!! $visitor->bezOpmerking !!}</div>
</div>

