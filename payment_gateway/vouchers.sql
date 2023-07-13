create table vouchers
(
    voucher_id int auto_increment
        primary key,
    code       varchar(100)                                        not null,
    category   int                                                 not null,
    duration   int                                                 not null comment 'code duration (hours)',
    cap        int                                                 not null,
    status     enum ('purchased', 'available') default 'available' not null,
    constraint vouchers_ibfk_1
        foreign key (category) references voucher_category (category)
            on update cascade on delete cascade
);

create index category
    on vouchers (category);

