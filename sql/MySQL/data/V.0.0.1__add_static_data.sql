-- Create sample client
INSERT INTO `oauth_client` (`client_id`, `name`, `secret`, `redirect_url`)
VALUES ('b6daa7e9-ebb7-4b97-9f4c-61615f2de94d', 'tester', 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12', 'http://localhost/OAuth2/');

-- Base client roles
INSERT INTO `oauth_grant` (`name`, `description`)
VALUES ('authorization_code', 'Allows to use authorization code grant type'),
       ('implicit', 'Allows to use implicit grant type'),
       ('password', 'Allows to use password grant type'),
       ('refresh_token', 'Allows to use refresh token grant type'),
       ('client_credentials', 'Allows to use client credentials grant type');

-- Allow default client to use everything
INSERT INTO `oauth_client_grant` (`client_id`, `grant_id`)
VALUES ('b6daa7e9-ebb7-4b97-9f4c-61615f2de94d', 1),
       ('b6daa7e9-ebb7-4b97-9f4c-61615f2de94d', 2),
       ('b6daa7e9-ebb7-4b97-9f4c-61615f2de94d', 3),
       ('b6daa7e9-ebb7-4b97-9f4c-61615f2de94d', 4),
       ('b6daa7e9-ebb7-4b97-9f4c-61615f2de94d', 5);
