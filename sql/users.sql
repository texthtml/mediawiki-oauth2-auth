--
-- extension MediaWikiOAuth2 users SQL schema
--
CREATE TABLE /*_*/"mediawiki_oauth2_users" (
  "external_id" VARCHAR(255) NOT NULL PRIMARY KEY,
  "internal_id" INTEGER NOT NULL UNIQUE
);
