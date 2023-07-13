create table purchased_voucher
(
    purchase_id      int auto_increment
        primary key,
    reference_number varchar(12)                          not null,
    user_id          int                                  not null,
    voucher_id       int                                  not null,
    date_created     datetime default current_timestamp() not null,
    constraint purchased_voucher_ibfk_1
        foreign key (user_id) references users (user_id)
            on update cascade on delete cascade,
    constraint purchased_voucher_ibfk_2
        foreign key (voucher_id) references vouchers (voucher_id)
            on update cascade on delete cascade
);

create index user_id
    on purchased_voucher (user_id);

create index voucher_id
    on purchased_voucher (voucher_id);

