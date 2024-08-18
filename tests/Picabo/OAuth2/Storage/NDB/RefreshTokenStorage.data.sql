INSERT INTO `oauth_user` (`user_id`, `username`, `password`)
VALUES ('5fcb1af9-d5cd-11', 'test', sha1('password'));

INSERT INTO `oauth_client` (`client_id`, `name`, `secret`, `redirect_url`)
VALUES ('d3a213ad-7b7a-11', 'test', sha1('password'), '');
