# Liens utiles

## Accès aux différents applicatifs

  * **API Docs (HTTP):** http://api-cinema.dev.boiteimmo.fr:11280/v1/docs
  * **Adminer:** http://api-cinema.dev.boiteimmo.fr:11282
    * Serveur: api-cinema-mariadb
    * Utilisateur: root
    * Mot de passe: root
  
    ## utilisation API
  * **identification:** http://api-cinema.dev.boiteimmo.fr:11280/v1/auth
    * method: POST
    * body: ** {"username": "admin","password": "admin"}
    * header: Content-Type : application/json

      * **exemple utilisation API**
            * lien pour recuperer liste des movies :  
              * Lien http://api-cinema.dev.boiteimmo.fr:11280/v1/movies
              * Method: GET
              * headers : X-Disable-Cache true, Content-Type : application/json
  * pour creation / modification 
      * * lien pour recuperer liste des movies :
          * Lien http://api-cinema.dev.boiteimmo.fr:11280/v1/movies
          * Method: POST/PUT
          * headers : Bearer {token} ( a recuperer de lien http://api-cinema.dev.boiteimmo.fr:11280/v1/auth)
              * Authorization: 
              * X-Disable-Cache true, 
              * Content-Type : application/json
      * Creation un movie
        * lien : http://api-cinema.dev.boiteimmo.fr:11280/v1/movies
```json lines I'm A tab
    {
        "title": "Toc Toc Docteura",
        "duration": 100,
        "types":[
            {"id": 1}
        ]
    }
```
```TODO
    finalisation enregistrement des peoples
    fixutes Data
    supscriber de recuperation d'image a la modification/creation de movie
```TODO
