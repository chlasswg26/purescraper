# purescraper
 API Scraper Apkpure.com Crafted with Some FCKNG GUY


## Scraper ApkPure.com (For Bypass purpose)
**Get Language** :
- /language

**Get Search** : (Opsi pilihan bisa ditambahkan 'id' Language)
- /search/hago (Query Text only)
- /id/search/hago

**Get Developer** : (Opsi pilihan bisa ditambahkan 'id' Language)
- /developer/{code} ('code' pada query array `[DEVELOPER][CODE]`)
- /developer/moonton (Opsional Non 'code')
- /id/developer/{code}
- /id/developer/Moonton

**Get Detail** :
- /detail/{code} ('code' pada query array `[APP][CODE]`)
- /id/detail/{code}

**Download Files** :
- /download/{code} ('code' pada query array `[APP][CODE]`)

>**Catatan untuk 'code' pada Array **
- Get Language ~~[APP][CODE]~~
- Get Search ~~[APP][CODE]~~
- Get Developer `[DEVELOPER][CODE]` diambil dari query array (Get Search)
- Get Detail `[APP][CODE]` diambil dari query array (Get Search / Get Developer)
- Download Files `[APP][CODE]` diambil dari query array (Get Detail)
