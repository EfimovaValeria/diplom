create EXTENSION IF NOT exists pgcrypto;

CREATE TABLE "role" (
    "role_id" SERIAL PRIMARY KEY,
    "role" VARCHAR(100) NOT null,
    "roleRus" VARCHAR(100) NOT null
);
CREATE TABLE "class" (
    "class_id" SERIAL PRIMARY KEY,
    "class" VARCHAR(100) NOT null
);

CREATE TABLE "student" (
    "student_id" SERIAL PRIMARY KEY,
    "surname" VARCHAR(100) NOT NULL,
    "firstname" VARCHAR(100) NOT NULL,
    "patronymic" VARCHAR(100) NOT NULL,
    "email" VARCHAR(100) NOT null UNIQUE,
  	"password" VARCHAR(100) NOT NULL,
    "role" INT REFERENCES "role"("role_id") NOT null,
    "class" INT REFERENCES "class"("class_id") NOT null,
    "tutor_id" INT REFERENCES "tutor"("tutor_id") NOT NULL
);

CREATE TABLE "parent" (
    "parent_id" SERIAL PRIMARY KEY,
    "surname" VARCHAR(100) NOT NULL,
    "firstname" VARCHAR(100) NOT NULL,
    "patronymic" VARCHAR(100) NOT NULL,
    "email" VARCHAR(100) NOT null UNIQUE,
  	"password" VARCHAR(100) NOT NULL,
    "role" INT REFERENCES "role"("role_id") NOT null,
    "student_id" INT REFERENCES "student"("student_id") NOT NULL
);

CREATE TABLE "tutor" (
    "tutor_id" SERIAL PRIMARY KEY,
    "surname" VARCHAR(100) NOT NULL,
    "firstname" VARCHAR(100) NOT NULL,
    "patronymic" VARCHAR(100) NOT NULL,
    "email" VARCHAR(100) NOT null UNIQUE,
  	"password" VARCHAR(100) NOT NULL,
    "role" INT REFERENCES "role"("role_id") NOT null
);

CREATE TABLE "subject" (
	"subject_id" SERIAL PRIMARY KEY,
	"subject_name" VARCHAR(100) NOT null UNIQUE
);

CREATE TABLE "subjectclass" (
	"subjectclass_id" SERIAL PRIMARY KEY,
	"class_id" int REFERENCES "class"("class_id") NOT NULL,
	"subject_id" int REFERENCES "subject"("subject_id")
);

CREATE TABLE "curriculum" (
	"curr_id" SERIAL PRIMARY KEY,
	"subjectclass_id" int REFERENCES "subjectclass"("subjectclass_id") NOT NULL,
	"lessonnum" VARCHAR(100) NOT null,
	"tema" VARCHAR(100) NOT null,
	"soderj" TEXT NOT null,
	"link" VARCHAR(255) NOT null
);

CREATE TABLE "schedule" (
	"schedule_id" SERIAL PRIMARY KEY,
	"student_id" INT REFERENCES "student"("student_id") NOT null,
	"date" date not null,
	"weekday" varchar(100) not null,
	"starttime" TIME not null,
 	"subjectclass_id" int REFERENCES "subjectclass"("subjectclass_id") NOT null,
	"curr_id" int references "curriculum"("curr_id") not null
 );   
