# Changelog

Alla viktiga ändringar i detta projekt dokumenteras i denna fil.

## [0.2.0] – 2025-08-06

### Added

* Implementerade paketöversikt i admin. Sektionen “Packages” visar nu alla definierade repositoryn med information om huruvida motsvarande plugin eller tema är installerat samt vilken version som är installerad. Senaste version och uppdateringsfunktioner kommer i en senare version.

### Changed

* Uppdaterade pluginversion till 0.2.0.

## [0.3.0] – 2025-08-06

### Added

* Paketöversikten visar nu den senaste release-versionen från GitHub för varje definierat repo. Detta hämtas via GitHub API och kräver att en giltig token sparats i inställningarna.

### Changed

* Uppdaterade pluginversion till 0.3.0.

## [0.3.1] – 2025-08-06

### Added

* Uppdaterade pluginförfattare i pluginhuvudet till “Thomas & Effie” för att ge rätt cred till utvecklarna.
* Lade till en sektion i README om att alltid ange ursprung och föregående utvecklare när projektet bygger på någon annans kod.

### Changed

* Uppdaterade pluginversion till 0.3.1.

## [0.4.0] – 2025-08-06

### Added

* Visar nu pluginets egna versionsnummer på inställningssidan så att du kan se att rätt version körs.
* Förbättrad pakethantering: GitFetch försöker hämta senaste release‑version via GitHub API och visar ett felmeddelande om API‑anropet misslyckas, till exempel på grund av ogiltig token eller nätverksproblem.
* Implementerad enkel typdetektion för repositoryn: om du inte anger plugin/tema när du lägger till ett repo så gissas typen baserat på om repots namn börjar med `plugin` eller `theme`.

### Changed

* Uppdaterade pluginversion till 0.4.0.


## [0.1.0] – 2025-08-06

### Added

* Första versionen av GitFetch med grundläggande struktur.
* Inställningssida för att spara GitHub‑token.
* GUI för att lägga till och ta bort repo med ägare, namn och typ.
* Skelett för GitHub API‑klient och Upgrader.
* Rensningsrutin vid avinstallation som tar bort plugin‑specifika alternativ.
* Licensfil (GPLv2) och README.

## Older versions

Ingen.