Changelog
======

v1.0
-----
**Nieuw**:
- Dupliceren van CV's, profielen en projecten is nu mogelijk
- Project: Een project kan nu ook een naam gegeven worden
- Gebruikers: Er wordt automatisch een mail gestuurd na 3 maanden van inactiviteit
- Gebruikers: Je kan nu je wachtwoord resetten als je deze bent vergeten
- Gebruikers: Nieuwe gebruikers krijgen automatisch een e-mail met hun (tijdelijke) wachtwoord
- Admin: Gebruikers kunnen nu snel geactiveerd en gedeactiveerd worden

**Bugfixes**:
- Personalia: Foto's worden nog kleiner gemaakt bij het uploaden
- Personalia: Foto's krijgen een willekeurige naam om speciale tekens te voorkomen
- Opleidingen: Het lege veld bij specialisatie wordt nu goed gevalideerd
- Projecten/Profielen: Betere foutmelding bij te veel tekens in het vak

v0.9
-----
**Nieuw**:
- Zoekfunctie in de linkerbalk toegevoegd om consultants te zoeken
- Landinstellingen SteVee-app aangepast naar NL

**Bugfixes**:
- Personalia: Problemen met het aanpassen van de geboortedatum zijn verholpen
- CV: Een maximaal van 15 checkboxes kan geselecteerd worden voor "Competenties"
- CV: Lettergrootte profiel in het CV verhoogd naar 9pt, conform de rest van het CV
- CV: De grootte van de profielfoto is afhankelijk gemaakt van de breedte van de foto, in plaats van de hoogte
- CV: Exporteren naar PDF geeft geen tabelranden meer weer
- CV: Publicaties zijn correct uitgelijnd
- CV: Een lange functietitel en klantnaam zullen bij de projecten niet meer overlappen
- Competenties: Het toevoegen van unieke competenties is nu correct geïmplementeerd
- Competenties: De slider werkt weer naar behoren
- SteVee: De kleurstelling is aangepast om de huidige huisstijl van Stevin beter te reflecteren
- Datum: Er is een alternatief datumveld geïmplementeerd om aan de behoefte van zowel Chrome als IE/Firefox gebruikers te kunnen voldoen

v0.6
-----
**Nieuw**:
- Alle schermen: Knop "Annuleren" toegevoegd naast "Opslaan"
- CV: Projecten worden weergegeven met Functietitel en Klant
- Alle tekstvelden (Profiel en Project): Spellchecker aangezet
- Tags: Tags kunnen in het administratiepaneel
- Tags: Voorspelling voor tags toegevoegd op basis van tags in de database


**Bugfixes**:
- Datum: Einddatum moet nu na de startdatum liggen
- Powerpoint: Tekst 'justified' (uitlijning)
- Project: Velden 'Situatie', 'Werkzaamheden' en 'Resultaat' hebben maximaal respectievelijk 325, 525 en 725 karakters zodat dit in het CV past
- Tags: Tags kunnen meer karakters aan
- Skills: Skills kunnen meer karakters aan
- Skills: Skills zijn uniek per persoon
- Datum: Alle datepickers zijn verwijderd. Het formaat is nu dd-mm-jjjj
- HTTP 500: Deze fout komt veelvuldig voor. Er is server-side validatie toegepast op alle velden, waardoor deze foutmelding niet meer zou moeten voorkomen


v0.2
-----
**Bugfixes**:
- Personalia: Fotoknop is uitgeschakeld als de knop "Bewerken" nog niet is ingedrukt
- Personalia: Geboortedatum kan nu aangepast worden
- Personalia: Foto uploadlimiet verhoogd. Ook wordt de foto nu automatisch naar ongeveer 3MB geschaald
- Tags: Tags kunnen meer karakters aan
- Skills: Skills kunnen meer karakters aan
- Skills: Skills zijn uniek per persoon
- Datum: Alle datepickers zijn verwijderd. Het formaat is nu dd-mm-jjjj
- HTTP 500: Deze fout komt veelvuldig voor. Er is server-side validatie toegepast op alle velden, waardoor deze foutmelding niet meer zou moeten voorkomen




v0.1
-----
Eerste versie SteeVee met de volgende functionaliteit:
- Inloggen
- Aanmaken/wijzigen/verwijderen van Projecten
- Aanmaken/wijzigen/verwijderen van Profielen
- Aanmaken/wijzigen/verwijderen van Opleidingen
- Aanmaken/wijzigen/verwijderen van Nevenactiviteitein
- Aanmaken/wijzigen/verwijderen van Publicaties
- Aanmaken/wijzigen/verwijderen van Competenties
- Aanmaken/wijzigen/verwijderen van Certificaten
- Aanmaken/wijzigen/verwijderen van CV's
- Wijzigen van Personalia
- Aanmaken/wijzigen/verwijderen van gebruikers (admin)
- Wijzigen wachtwoord
- Weergeven gegevens andere consultants
- Wijzigen gegevens andere consultants (admin)
- Uitdraaien Powerpoint CV
- Uitloggen
