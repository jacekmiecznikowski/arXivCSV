# arXivCSV

This PHP script creates CSV files from OAI2 arXiv data.

## How to use
Just put your XML files downloaded from arXiv.org to ./xml directory and start script:
```
php arXivCSV.php
```

## Where find XML-s?
1. You can download them:
* [figshare](https://figshare.com/s/3378e00e2362f5ac5e4d) - Link to figshare repository.
2. You can aquire them using this:
* [oai-harvest](https://github.com/jacekmiecznikowski/oai-harvest) - A harvester used to collect records from arXiv via OAI2.


## How to import them into Neo4j?
```
# Categories
CREATE INDEX ON :Category(name)
LOAD CSV WITH HEADERS FROM "file:///categories.csv" AS line WITH line
CREATE(category:Category {name:line.category})
WITH category, SPLIT(line.subcategory, ";") AS subcategories
UNWIND subcategories AS sub
MERGE(subcategory:Category {name:sub})
MERGE (subcategory)-[:WITHIN]->(category)

# Publications
CREATE INDEX ON :Article(id)
CREATE INDEX ON :Article(created)
USING PERIODIC COMMIT
LOAD CSV WITH HEADERS FROM "file:///publications.csv" AS line WITH line
CREATE (article:Article { 
id: toInteger(line.id), 
title: line.title, 
abstract: line.abstract, 
url: line.url, 
created: line.date })
WITH article, SPLIT(line.categories, ";") AS subjects
UNWIND subjects AS subject
MATCH (category:Category{name: subject})
CREATE (article)-[:WITHIN]->(category)


# Authors
CREATE INDEX ON :Scientist(first_name, last_name)
USING PERIODIC COMMIT
LOAD CSV WITH HEADERS FROM "file:///authors.csv" AS line WITH line
MATCH (article:Article { 
id: toInteger(line.id)}) WITH line, article
MERGE (author:Scientist{first_name: line.first_name, last_name: line.last_name})
WITH article, author
MERGE (author)-[:PUBLISHED]->(article)

# Citations
USING PERIODIC COMMIT
LOAD CSV WITH HEADERS FROM "file:///citations.csv" AS line WITH line
MATCH (src:Article { id: toInteger(line.src)}) WITH line, src
MATCH (dst:Article { id: toInteger(line.dst)}) WITH src, dst
MERGE (src)-[:CITES]->(dst)
```

## Citations
Citations can be simulated using [arXivCite](https://github.com/jacekmiecznikowski/arxivCite)
