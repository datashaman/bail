# bail

POC semantic search for _Laravel_ and _PostgreSQL_ with _pgvector_. _OpenAI_'s `text-embedding-3-small` model is used for the embeddings.

Embedding responses are cached for 7 days to avoid repeated calls with the same content.

## requirements

* postgresql 14+
* _OpenAI_ API key

## setup

```
composer install
cp .env.example .env
$EDITOR .env // set your OpenAI api key
php artisan migrate
```

## seed

There are some basic documents in the seeders. You can index your own documents (see below).
```
php artisan db:seed
```

## actions

### index

```
Description:
  Index documents.

Usage:
  index [options] [--] <filename>

Arguments:
  filename              The filename of the documents to index

Options:
      --chunk[=CHUNK]   Chunk size of the documents [default: "5"]
      --limit[=LIMIT]   Limit the number of documents to index
      --delete          Delete all documents before indexing
```

where `filename` is a JSON file with the following structure:
```json
[
    {
      "content": "This is a document",
      "meta": {
          "key1": "value1"
      }
    },
    {
      "content": "This is another document",
      "meta": {
          "key2": "value2"
      }
    }
]
```

### search

```
Description:
  Search documents

Usage:
  search [options] [--] <query>...

Arguments:
  query                      The query to search for

Options:
      --per-page[=PER-PAGE]  The number of results to return per page [default: "10"]
      --page[=PAGE]          The page number to return [default: "1"]
      --embedding            Use pgvector search, default is text search
```

For example:
```
php artisan search I want a big house with a huge kitchen
```
