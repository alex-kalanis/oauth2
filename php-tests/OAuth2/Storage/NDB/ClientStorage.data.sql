INSERT INTO `oauth_client` (`client_id`, `name`, `secret`, `redirect_url`)
VALUES ('afa233ad-5142-32', 'test', sha1('password'), ''),
       ('d3a213ad-d142-11', 'can_grant', sha1('support'), '');

INSERT INTO `oauth_client_grant` (`client_id`, `grant_id`)
VALUES ('d3a213ad-d142-11', 5);
