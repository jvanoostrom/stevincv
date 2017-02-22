Changelog
======
Deze GitHub Repo bevat de broncode van de Stevin CV Tool, van Stevin Technology Consultants. Met deze repo kan worden samengewerkt aan de tool.

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
- Project: Velden 'Situatie', 'Werkzaamheden' en 'Resultaat' hebben maximaal respectievelijk 325, 525 en 725 karakters zodat dit in het CV past.
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
