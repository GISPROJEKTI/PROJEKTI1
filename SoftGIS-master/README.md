## Vaatimukset

- HTTP-palvelin
 - Apachea käytetty
 - Periaatteessa mikä vain, jossa CakePHP toimii
- Tietokantaohjelmisto
 - MySQL:n kanssa käytetty
 - Periaatteessa mikä vain CakePHP:n tukema
- PHP5
- [CakePHP:n vaatimukset](http://book.cakephp.org/1.3/en/view/908/Requirements)

## Asennusohjeet

1. Asenna HTTP-palvelin, tietokantaohjelmisto ja PHP
2. [Lataa](https://github.com/cakephp/cakephp/archive/1.3.10.zip) ja asenna CakePHP **versio 1.3.10 ja 1.3.15 todettu toimiviksi**. CakePHP:n yleiset asennuseohjeet [löytyvät täältä](http://book.cakephp.org/1.3/en/view/912/Installation).
3. Lataa SoftGIS paketti [uudempi](https://github.com/GISPROJEKTI/PROJEKTI1) tai [aiempi versio](https://github.com/lanttu/SoftGIS).
4. Pura SoftGIS paketti CakePHP:n asennuksen päälle niin että tiedosto `index.php` ja kansio `app/` korvaantuvat.
5. Asetukset
 1. Mene `app/config` kansioon
 2. Nimeä uudelleen `core.php.default` -> `core.php`
 3. Nimeä uudelleen `database.php.default` -> `database.php`
 4. Aseta tietokannan asetukset `database.php` tiedostoon
 5. Aseta oma security salt `core.php` tiedostoon
6. Tietokannan luonti CakePHP:n console työkalulla
 1. Mene `app/` kansioon
 2. Aja käsky `../cake/console/cake schema create -file app.php App`
 3. Käsky luo tietokantaan tarvittavat taulut
 4. [Ohjeita consolen käyttöön](http://book.cakephp.org/1.3/en/view/1521/Core-Console-Applications)
 5. Jos et saa konsolia toimimaan voit luoda tietokannan käsin `Gis tietokannan taulujen luontikomennot.txt` tiedoston avulla.
7. Muut asetukset
 1. Aseta riittävät käyttöoikeudet väliaikaiskansioihin: `app/tmp`, `app/tmp/cache`, `app/tmp/cache/models`, `app/tmp/cache/presistent` ja `app/tmp/cache/views`.
 2. Jos sivusto toimii, hyvä. Jos ei, niin voi olla että palvelimesi mod_rewrite (.htaccess) toiminto ei ole käytössä. [Lisätietoja.](http://book.cakephp.org/1.3/en/The-Manual/Developing-with-CakePHP/Installation.html)

## CakePHP:n versio

CakePHP versio 1.3.10
