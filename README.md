# bail

POC semantic search for Laravel and PostgreSQL with pgvector. OpenAI's `text-embedding-3-small` model is used for the embeddings.

## requirements

* postgresql 14+

## setup

```
composer install
cp .env.example .env
$EDITOR .env // set your OpenAI api key
php artisan migrate
```

## seed

There are some basic documents in the seeders:
```
php artisan db:seed
```

## actions

### index

Index documents with this command:
```
php artisan index {filename.json}
```
where `filename.json` is a JSON file with the following structure:
```json
[
    {
      "title": "A title",
      "content": "This is a document"
    },
    {
      "title": "Another title",
      "content": "This is another document"
    }
]
```

### search

Search documents with this command:
```
php artisan search {query*}
```
where `query*` is a list of words to search for.

For example:
```
php artisan search I want a big house with a huge kitchen
```
