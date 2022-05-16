DROP TABLE users;
CREATE TABLE users (id integer primary key, name text, email text, username text, password text, household text);

DROP TABLE households;
CREATE TABLE households (id integer primary key, householdName text, passcode text);

DROP TABLE chores;
CREATE TABLE chores (id integer primary key, choreName text, choreDescription integer, choreFrequency integer, choreStatus integer, deadlineDate text, notificationDate text, choreUser text, householdName text);