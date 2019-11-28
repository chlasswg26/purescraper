# purescraper
 API Scraper Apkpure.com Crafted with Some FCKNG GUY


## Scraper ApkPure.com (For Bypass purpose)
**Get Language** :
- /apkpure/language

**Get Search** : (Opsi pilihan bisa ditambahkan 'id' Language)
- /apkpure/search/hago (Query Text only)
- /apkpure/id/search/hago

**Get Developer** : (Opsi pilihan bisa ditambahkan 'id' Language)
- /apkpure/developer/{code} ('code' pada query array `[DEVELOPER][CODE]`)
- /apkpure/developer/moonton (Opsional Non 'code')
- /apkpure/id/developer/{code}
- /apkpure/id/developer/Moonton

**Get Detail** :
- /apkpure/detail/{code} ('code' pada query array `[APP][CODE]`)
- /apkpure/id/detail/{code}

**Download Files** :
- /apkpure/download/{code} ('code' pada query array `[APP][CODE]`)

>**Catatan untuk 'code' pada Array **
- Get Language ~~[APP][CODE]~~
- Get Search ~~[APP][CODE]~~
- Get Developer `[DEVELOPER][CODE]` diambil dari query array (Get Search)
- Get Detail `[APP][CODE]` diambil dari query array (Get Search / Get Developer)
- Download Files `[APP][CODE]` diambil dari query array (Get Detail)
