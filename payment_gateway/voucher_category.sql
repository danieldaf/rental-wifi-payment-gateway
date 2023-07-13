create table voucher_category
(
    category            int auto_increment
        primary key,
    price               int(10) not null,
    voucher_description text    not null
);

