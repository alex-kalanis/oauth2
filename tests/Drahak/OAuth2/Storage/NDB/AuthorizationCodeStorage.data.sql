INSERT INTO oauth_user (id, username, password)
VALUES ('5fcb1af9-d5cd-11', 'test', sha1('password'));

INSERT INTO oauth_scope (name, description)
VALUES ('profile', 'Allow access to public profile information');