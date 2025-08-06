# GitFetch

GitFetch är ett WordPress‑plugin som låter dig ansluta till privata GitHub‑repositorier, jämföra versioner av teman och plugins, se README och CHANGELOG och installera uppdateringar direkt från admin. Pluginet är designat för egenutvecklade projekt där versioner lagras i GitHub och distribueras utanför WordPress.org.

## Funktioner

* Lagra en GitHub Personal Access Token för autentiserade API‑anrop.
* Lägg till flera GitHub‑repo via admin och märk dem som plugin eller tema – du behöver aldrig redigera kod för att lägga till eller ändra ett repo.
* Visa en lista över dina repo med möjlighet att ta bort dem.
* Tydlig angivelse av vilket repo varje paket tillhör i översiktstabellen; du ser alltid namnet på det repo du arbetar mot.
* (Planned) Jämför installerad version med senaste release på GitHub och installera uppdateringar eller tidigare versioner.
* (Planned) Visa `README.md` och `CHANGELOG.md` från repo i admin.
* (Planned) Rensa gamla plugin‑data vid avinstallation.

## Installation

1. **Ladda ner och installera** zip-filen för GitFetch via **Tillägg → Lägg till nytt → Ladda upp tillägg** i WordPress.
2. **Aktivera** pluginet.
3. **Skapa en GitHub Personal Access Token**:
   - Se till att din e‑postadress är verifierad i GitHub.
   - Klicka på din profilbild uppe till höger på github.com och välj **Settings**【948104679981302†L356-L362】.
   - Gå till **Developer settings** och välj **Personal access tokens → Tokens (classic)**【948104679981302†L359-L364】.
   - Klicka **Generate new token (classic)**. Skriv ett beskrivande namn, ställ in giltighetstid och välj scope **repo** (för privata repos) eller *public_repo* (för offentliga repos)【948104679981302†L365-L372】.
   - Klicka **Generate token** och kopiera tokenen【948104679981302†L375-L377】.
4. **Lägg in tokenen i GitFetch**: Gå till **GitFetch** i administratörsmenyn, klistra in tokenen i fältet *GitHub Personal Access Token* och klicka **Spara token**.
5. **Lägg till ett repository**: Fyll i ägare (owner), repots namn och välj om det är ett plugin eller tema. Klicka **Lägg till repo**. Du kan när som helst ta bort ett repo via *Delete*‑knappen.
6. (Kommande) GitFetch kommer att jämföra installerad version med den senaste releasen, visa README och CHANGELOG i admin och låta dig uppdatera eller nedgradera med ett klick.

## Steg‑för‑steg användarguide

1. **Installera GitFetch** som vilket annat WordPress‑plugin (se Installation ovan).
2. **Generera en GitHub‑token** enligt instruktionerna i punkt 3 i Installation.
3. **Spara tokenen** i GitFetch‑inställningarna.
4. **Lägg till de repos du vill följa** och ange deras typ.
5. **Följ versioner**: I kommande versioner kommer GitFetch att visa om det finns nya versioner att installera.
6. **Ta bort repos**: Klicka på *Delete* bredvid repot i listan om du inte längre vill följa det.

> **Observera:** GitFetch är avsett för privat användning och följer inte WordPress.orgs regler för distribution av plugins som installerar kod från externa källor【38248605680118†L256-L263】. Ditt GitHub‑konto måste ha behörighet till de repo du lägger till.

## Vanliga frågor

### Vilka är kraven?

* WordPress 6.0 eller senare.
* PHP 7.4 eller senare.
* En GitHub Personal Access Token med tillgång till de repo du vill använda.

### Är pluginet säkert att använda?

Pluginet använder WordPress HTTP‑API för alla anrop【655874743635771†L70-L100】 och kontrollerar paketet via WordPress’ inbyggda uppgraderingsklass【659666605620511†L96-L156】. Det är dock fortfarande ditt ansvar att säkerställa att koden i ditt repo är trygg.

### Hur kan jag bidra?

Du kan bidra med buggrapporter, förslag eller pull requests på GitHub‑projektet.

## Licens

GitFetch är fri programvara och licensieras under GPLv2 eller senare. Se `LICENSE` för fullständig licenstext.