# Trustindex – Medior PHP Fejlesztői Tesztfeladat

Ez egy leegyszerűsített, de valós üzleti logikát tükröző cégértékelő minialkalmazás. A felhasználók nyilvános véleményeket írhatnak cégekről, böngészhetik a meglévő értékeléseket, valamint aggregált statisztikai kimutatást láthatnak a regisztrált vállalatok teljesítményéről.

## Technológiai Stack
- **PHP:** 8.2+
- **Keretrendszer:** Symfony 7.4.* (webapp architektúra)
- **Adatbázis & ORM:** Doctrine ORM + Migrations (fejlesztéshez és teszteléshez **SQLite**-ot használunk a könnyű hordozhatóság és a zéró-konfigurációs bírálat érdekében)
- **Megjelenítés:** Twig sablonmotor + Material Design (MDBootstrap 5 UI Kit)
- **Tesztelés:** PHPUnit 11

---

## Főbb  Megoldások & Extrák (Clean Code & DRY)

- **Automata Időbélyegek (Doctrine Lifecycle Callbacks):** A `Review` entitás a `#[ORM\HasLifecycleCallbacks]` attribútum segítségével, a `PrePersist` és `PreUpdate` eseményeknél maga menedzseli a `created_at` és `updated_at` mezőket, így a kontroller mentesül a felesleges technikai logikától.
- **Adatbázis-szintű Aggregáció:** A kötelező cégstatisztikák számítását (átlag és darabszám) nem PHP memóriában végezzük. A `ReviewRepository`-ban egy optimalizált DQL (Doctrine Query Language) csoportosított lekérdezés fut le, ami nagy adatmennyiség esetén is villámgyors.
- **Post-Redirect-Get (PRG) Minta:** Az új vélemény sikeres beküldése után a kontroller azonnal HTTP 302-es átirányítást végez, megakadályozva ezzel, hogy a felhasználó az oldal frissítésével (F5) duplikált adatokat küldjön be.
- **BÓNUSZ – Keresőrendszer (2.5):** A cég név alapú szűrés dinamikusan integrálva lett mind a főoldali véleményáradatba, mind a `/companies` statisztikai táblázatba.
- **BÓNUSZ EXTRA – Kritikus Értékelés Figyelő (2.6):** Beépítésre került egy különálló `BadReviewSubscriber` (Doctrine Event Subscriber). Ha egy cég 1 vagy 2 csillagos (negatív) értékelést kap, az eseménykezelő automatikusan elkapja a folyamatot és biztonságosan naplózza (Monolog), ami éles környezetben azonnali Slack/Email riasztások alapja.
- **Anyagelvű Felület (Material Design UX):** A frontend szakít a natív HTML/Bootstrap formok világával; a lebegő input címkék és az animált ripple-effektek prémium felhasználói élményt nyújtanak.

---

## Telepítés és Futtatás lépésről lépésre

### 1. Repository klónozása
```bash
git clone <repository-url>
cd trustindex-test
composer install
composer require --dev friendsofphp/php-cs-fixer
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
vendor/bin/php-cs-fixer fix src
vendor/bin/php-cs-fixer fix tests
php -S 127.0.0.1:8000 -t public
```

### Tesztelés

- **A projekt átfogó minőségbiztosításáról izolált Unit teszt (Entity getter/setter és lifecycle viselkedés) és komplex Funkcionális integrációs tesztek (WebTestCase) gondoskodnak. Utóbbiak teljesen tiszta, izolált, memóriában felépített SQLite sémával tesztelik az űrlap validációját, a sikeres beküldést, valamint az átlagszámítási és rendezési üzleti logikát.**

### Tesztelés
```bash
php bin/phpunit
```