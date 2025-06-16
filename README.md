# ESGI Back API

## Creating an annonce with photos

Send a `POST` request to `/api/annonces` with JSON body like:

```json
{
  "titre": "Titre de l'annonce",
  "description": "Description",
  "prix": "100.00",
  "statut": "en ligne",
  "dateCreation": "2025-05-28 12:00:00",
  "photos": [
    {"urlChemin": "http://example.com/p1.jpg"},
    {"urlChemin": "http://example.com/p2.jpg", "dateUpload": "2025-05-28 12:00:00"}
  ]
}
```

Each object in `photos` must include `urlChemin`. The optional `dateUpload` sets the upload date. The response returns the created annonce ID and the list of photo URLs.
