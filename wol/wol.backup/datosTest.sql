/* 
pass a => a, b => b, c => c
a => admin
b, c => users

4 hosts : a owns 1, b 2 & 3, c 4
a => view and power all
b and c => view and power their computers

b, c => farm1
a, b => farm2

hosts 2, 4 => farm1 ; 3, 4 => farm2

*/

INSERT INTO php_wol_users (login, pass, email) VALUES ('aaaaa', '0cc175b9c0f1b6a831c399e269772661', 'a@a.es');
INSERT INTO php_wol_users (login, pass, email) VALUES ('bbbbb', '92eb5ffee6ae2fec3ad71c777531578f', 'b@b.es');
INSERT INTO php_wol_users (login, pass, email) VALUES ('ccccc', '4a8a08f09d37b73795649038408b5f33', 'c@c.es');

INSERT INTO php_wol_users_roles (id_role, id_user) VALUES (2, 1);
INSERT INTO php_wol_users_roles (id_role, id_user) VALUES (1, 2);
INSERT INTO php_wol_users_roles (id_role, id_user) VALUES (1, 3);

INSERT INTO php_wol_hosts (ip, mac, name, owner_id) VALUES ("10.10.0.232", "90e6ba32dac8", "blop", 1);
INSERT INTO php_wol_hosts (ip, mac, name, owner_id) VALUES ("10.10.0.14", "00163e04bdde", "couic", 2);
INSERT INTO php_wol_hosts (ip, mac, name, owner_id) VALUES ("10.10.0.5", "001e5830d1ac", "zig", 2);
INSERT INTO php_wol_hosts (ip, mac, name, owner_id) VALUES ("10.10.0.241", "001d7d9f13cf ", "zag", 3);

INSERT INTO php_wol_users_hosts (id_host, id_user) VALUES (1, 1);
INSERT INTO php_wol_users_hosts (id_host, id_user) VALUES (2, 1);
INSERT INTO php_wol_users_hosts (id_host, id_user) VALUES (3, 1);
INSERT INTO php_wol_users_hosts (id_host, id_user) VALUES (4, 1);


INSERT INTO php_wol_users_hosts (id_host, id_user, hostname_for_user) VALUES (2, 2, "Hola");
INSERT INTO php_wol_users_hosts (id_host, id_user, hostname_for_user) VALUES (3, 2, "Yeah");
INSERT INTO php_wol_users_hosts (id_host, id_user, hostname_for_user) VALUES (4, 3, "Hello host");

INSERT INTO php_wol_hostsfarms (hostfarm_name) VALUES ('myfirstfarm');
INSERT INTO php_wol_hostsfarms (hostfarm_name) VALUES ('mysecondfarm');

INSERT INTO php_wol_users_hostsfarms (id_user, id_hostsfarm) VALUES (2, 1);
INSERT INTO php_wol_users_hostsfarms (id_user, id_hostsfarm) VALUES (3, 1);
INSERT INTO php_wol_users_hostsfarms (id_user, id_hostsfarm) VALUES (1, 2);
INSERT INTO php_wol_users_hostsfarms (id_user, id_hostsfarm) VALUES (2, 2);

INSERT INTO php_wol_hostfarms_hosts (id_host, id_hostsfarm) VALUES (2, 1);
INSERT INTO php_wol_hostfarms_hosts (id_host, id_hostsfarm) VALUES (4, 1);
INSERT INTO php_wol_hostfarms_hosts (id_host, id_hostsfarm) VALUES (3, 2);
INSERT INTO php_wol_hostfarms_hosts (id_host, id_hostsfarm) VALUES (4, 2);

