create table user_details
(
    id         int          not null
        primary key,
    user_id    int          not null,
    first_name varchar(255) null,
    last_name  varchar(255) null
);

create index user_id
    on user_details (user_id);

