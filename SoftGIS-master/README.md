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
2. Lataa ja [asenna](http://book.cakephp.org/1.3/en/view/912/Installation) CakePHP **versio 1.3**. CakePHP:n yleiset asennuseohjeet löytyvät oheisesta linkistä.
3. Lataa SoftGIS paketti [Githubista](https://github.com/lanttu/SoftGIS).
4. Pura SoftGIS paketti CakePHP:n asennuksen päälle niin että tiedosto `index.php` ja kansio `app/` korvaantuvat.
5. Asetukset
 1. Mene `app/config` kansioon
 2. Nimeä uudelleen `core.php.default` -> `core.php`
 3. Nimeä uudelleen `database.php.default` -> `database.php`
 4. Aseta tietokannan asetukset `database.php` tiedostoon
6. Tietokannan luonti CakePHP:n console työkalulla
 1. Mene `app/` kansioon
 2. Aja käsky `../cake/console/cake schema create -file app.php App`
 3. Käsky luo tietokantaan tarvittavat taulut
 4. [Ohjeita consolen käyttöön](http://book.cakephp.org/1.3/en/view/1521/Core-Console-Applications)

## CakePHP:n versio

CakePHP versio 1.3.10
