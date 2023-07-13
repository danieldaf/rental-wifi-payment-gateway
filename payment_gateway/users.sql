create table users
(
    user_id      int                                  not null
        primary key,
    username     varchar(50)                          not null,
    email        varchar(255)                         not null,
    password     varchar(255)                         not null,
    date_created datetime default current_timestamp() not null,
    constraint users_ibfk_1
        foreign key (user_id) references user_details (user_id)
            on update cascade on delete cascade
);

