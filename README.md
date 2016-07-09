# Contributor Distance

REST api with one endpoint that returns the shortest distance between two github contributors to a package.

A package is named with a `{owner}/{repo}` tag.

To see the shortest distance between two contributors to a package, use the endpoint

```
 GET /shortest/{owner}/{repo}
```

When you get a 200 response, it will contain a json body like this:

```
{
  "distance": 14.3,
  "unit": "km"
}
```

The unit will always be km, but is included just in case we have some future extensions.


If you get a 4xx or 5xx response, the returned json body will look like this:

```
{
  "code": 1
  "message": "Github package xxxx/yyyy not found"
}
```

Each type of error message also has a unique code that goes with it, to make it easier for clients to parse it.

Here is a table of the possible error messages:

| HTTP status code | code | message |
|------------------|-----:|:--------|
| 404 |  1 | Github package {owner}/{repo} not found |
| 404 |  2 | Did not find two contributors with location information |
