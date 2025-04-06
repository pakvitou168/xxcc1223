create table if not exists public.action_events
(
    id              bigserial
        primary key,
    batch_id        char(36)                                         not null,
    user_id         bigint                                           not null,
    name            varchar(255)                                     not null,
    actionable_type varchar(255)                                     not null,
    actionable_id   bigint,
    target_type     varchar(255)                                     not null,
    target_id       bigint,
    model_type      varchar(255)                                     not null,
    model_id        bigint,
    fields          text                                             not null,
    status          varchar(25) default 'running'::character varying not null,
    exception       text                                             not null,
    created_at      timestamp(0),
    updated_at      timestamp(0),
    original        text,
    changes         text
);

alter table public.action_events
    owner to phillip;

create index if not exists action_events_actionable_type_actionable_id_index
    on public.action_events (actionable_type, actionable_id);

create index if not exists action_events_batch_id_model_type_model_id_index
    on public.action_events (batch_id, model_type, model_id);

create index if not exists action_events_user_id_index
    on public.action_events (user_id);

create table if not exists public.data
(
    id         bigserial
        primary key,
    data_type  text,
    data_msg   text,
    created_at timestamp with time zone,
    created_by text,
    updated_at timestamp with time zone,
    updated_by text
);

alter table public.data
    owner to phillip;

create table if not exists public.failed_jobs
(
    id         bigserial
        primary key,
    uuid       varchar(255)                           not null
        constraint failed_jobs_uuid_unique
            unique,
    connection text                                   not null,
    queue      text                                   not null,
    payload    text                                   not null,
    exception  text                                   not null,
    failed_at  timestamp(0) default CURRENT_TIMESTAMP not null
);

alter table public.failed_jobs
    owner to phillip;

create table if not exists public.fn_exchange_rate
(
    id          serial
        constraint "FN_EXCHANGE_RATE_PK"
            primary key,
    branch_code varchar(3),
    rate_type   text,
    rate_date   date,
    ccy1        varchar(3),
    ccy2        varchar(3),
    mid_rate    double precision,
    buy_rate    double precision,
    sale_rate   double precision,
    buy_spread  double precision,
    sale_spread double precision,
    rate_serial integer,
    status      varchar(3),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10),
    approved_at timestamp,
    approved_by varchar(10)
);

alter table public.fn_exchange_rate
    owner to phillip;

create table if not exists public.fn_fin_cycle
(
    id            serial
        constraint "FN_FIN_CYCLE_PK"
            primary key,
    fin_cycle     text,
    description   text,
    fc_start_date date,
    fc_end_date   date,
    status        varchar(3),
    created_at    timestamp default now(),
    created_by    varchar(10),
    updated_at    timestamp,
    updated_by    varchar(10)
);

alter table public.fn_fin_cycle
    owner to phillip;

create table if not exists public.fn_gl_master
(
    id         serial
        constraint "FN_GL_MASTER_PK"
            primary key,
    gl_code    text,
    gl_desc    text,
    parent_gl  text,
    leaf       varchar(1),
    category   text,
    status     varchar(3),
    created_at timestamp default now(),
    created_by varchar(10),
    updated_at timestamp,
    updated_by varchar(10)
);

alter table public.fn_gl_master
    owner to phillip;

create table if not exists public.fn_period_code
(
    id            serial
        constraint "FN_PERIOD_CODE_PK"
            primary key,
    period_code   text,
    fin_cycle     text,
    pc_start_date date,
    pc_end_date   date,
    status        varchar(3),
    created_at    timestamp default now(),
    created_by    varchar(10),
    updated_at    timestamp,
    updated_by    varchar(10)
);

alter table public.fn_period_code
    owner to phillip;

create table if not exists public.fn_trn_event
(
    id         serial
        constraint "FN_TRN_EVENT_PK"
            primary key,
    module     text,
    event_code text,
    event_desc text,
    status     varchar(3),
    created_at timestamp default now(),
    created_by varchar(10),
    updated_at timestamp,
    updated_by varchar(10)
);

alter table public.fn_trn_event
    owner to phillip;

create table if not exists public.fn_trn_event_template
(
    id               serial
        constraint "FN_TRN_EVENT_TEMPLATE_PK"
            primary key,
    module           text,
    trn_ref_no       text,
    event_code       text,
    ac_branch        varchar(3),
    ac_no            text,
    drcr_ind         varchar(1),
    amount_tag       text,
    ccy              varchar(3),
    related_customer text,
    external_ref_no  text,
    financial_cycle  text,
    period_code      text,
    category         text,
    cust_gl          text,
    status           varchar(3),
    created_at       timestamp default now(),
    created_by       varchar(10),
    updated_at       timestamp,
    updated_by       varchar(10)
);

alter table public.fn_trn_event_template
    owner to phillip;

create table if not exists public.fn_trn_log
(
    id               serial
        constraint "FN_TRN_LOG_PK"
            primary key,
    module           text,
    trn_ref_no       text,
    event_code       text,
    ac_branch        varchar(3),
    ac_no            text,
    drcr_ind         varchar(1),
    amount_tag       text,
    ccy              varchar(3),
    fcy_amount       double precision,
    exch_rate        double precision,
    lcy_amount       double precision,
    related_customer text,
    external_ref_no  text,
    trn_dt           date,
    financial_cycle  text,
    period_code      text,
    batch_no         text,
    curr_no          integer,
    category         text,
    cust_gl          text,
    auth_stat        text,
    delete_stat      text,
    user_id          text,
    save_timestamp   timestamp default now(),
    auth_id          text,
    auth_timestamp   timestamp
);

alter table public.fn_trn_log
    owner to phillip;

create table if not exists public.fn_trn_log_history
(
    id               integer not null
        constraint "FN_TRN_LOG_HISTORY_PK"
            primary key,
    module           text,
    trn_ref_no       text,
    event_code       text,
    ac_branch        varchar(3),
    ac_no            text,
    drcr_ind         varchar(1),
    amount_tag       text,
    ccy              varchar(3),
    fcy_amount       double precision,
    exch_rate        double precision,
    lcy_amount       double precision,
    related_customer text,
    external_ref_no  text,
    trn_dt           date,
    financial_cycle  text,
    period_code      text,
    batch_no         text,
    curr_no          integer,
    category         text,
    cust_gl          text,
    auth_stat        text,
    delete_stat      text,
    user_id          text,
    save_timestamp   timestamp,
    auth_id          text,
    auth_timestamp   timestamp
);

alter table public.fn_trn_log_history
    owner to phillip;

create table if not exists public.idm_application
(
    id                    serial
        constraint "IDM_APPLICATION_PK"
            primary key,
    name                  varchar(50),
    code                  varchar(10),
    description           varchar(250),
    app_type              varchar(8),
    access_lvl            integer,
    allow_change_username varchar(1) default 'N'::character varying,
    status                varchar(5),
    create_at             timestamp  default now(),
    create_by             varchar(10),
    update_at             timestamp,
    update_by             varchar(10)
);

alter table public.idm_application
    owner to phillip;

create table if not exists public.idm_branch
(
    branch_code     varchar(5),
    branch_name_en  varchar(250),
    branch_name_kh  varchar(250),
    alt_branch_code varchar(3),
    sequence        integer,
    status          varchar(5),
    create_at       timestamp default now(),
    create_by       varchar(200),
    update_at       timestamp,
    update_by       varchar(200)
);

alter table public.idm_branch
    owner to phillip;

create table if not exists public.idm_client
(
    id                      serial
        constraint "IDM_CLIENT_PK"
            primary key,
    client_id               varchar(100),
    client_secret           varchar(100),
    app_code                varchar(10),
    grant_type              varchar(100),
    scope                   varchar(100),
    redirect_uri            varchar(100),
    encode_key              varchar(100),
    jwt_key                 varchar(100),
    token_valid_sec         integer,
    refresh_token_valid_sec integer,
    oauth_key               varchar(50),
    enum_key                varchar(10),
    status                  varchar(10),
    create_at               timestamp default now(),
    create_by               varchar(200),
    update_at               timestamp,
    update_by               varchar(200)
);

alter table public.idm_client
    owner to phillip;

create table if not exists public.idm_function
(
    id          serial
        primary key,
    code        varchar(50),
    app_code    varchar(10),
    path        varchar(250),
    name        varchar(100),
    description varchar(250),
    parent      varchar(50),
    lvl         integer,
    sequence    integer,
    icon        varchar(50),
    permission  varchar(250),
    menu        varchar(1),
    status      varchar(5),
    url         varchar(250),
    create_at   timestamp,
    create_by   varchar(10),
    update_at   timestamp,
    update_by   varchar(10)
);

alter table public.idm_function
    owner to phillip;

create table if not exists public.idm_function_attr
(
    id          serial
        constraint "IDM_FUNCTION_ATTR_PK"
            primary key,
    function_id integer not null,
    app_code    varchar(10),
    name        varchar(100),
    key         varchar(20),
    value       varchar(250),
    status      varchar(5),
    create_at   timestamp default now(),
    create_by   varchar(10),
    update_at   timestamp,
    update_by   varchar(10)
);

alter table public.idm_function_attr
    owner to phillip;

create table if not exists public.idm_group
(
    id          serial
        constraint "IDM_GROUP_PK"
            primary key,
    code        varchar(50)  not null,
    name        varchar(100) not null,
    description varchar(250),
    is_default  varchar(1),
    status      varchar(5),
    create_at   timestamp default now(),
    create_by   varchar(10),
    update_at   timestamp,
    update_by   varchar(10)
);

alter table public.idm_group
    owner to phillip;

create table if not exists public.idm_group_attr
(
    id        serial
        constraint "IDM_GROUP_ATTR_PK"
            primary key,
    group_id  integer not null,
    name      varchar(100),
    key       varchar(20),
    value     varchar(250),
    status    varchar(5),
    create_at timestamp default now(),
    create_by varchar(10),
    update_at timestamp,
    update_by varchar(10)
);

alter table public.idm_group_attr
    owner to phillip;

create table if not exists public.idm_group_role
(
    id        serial
        constraint "IDM_GROUP_ROLE_PK"
            primary key,
    group_id  integer not null,
    role_id   integer not null,
    app_code  varchar(10),
    status    varchar(5),
    create_at timestamp default now(),
    create_by varchar(10),
    update_at timestamp,
    update_by varchar(10)
);

alter table public.idm_group_role
    owner to phillip;

create table if not exists public.idm_role
(
    id          serial
        constraint "IDM_ROLE_PK"
            primary key,
    code        varchar(16),
    app_code    varchar(10),
    name        varchar(100),
    description varchar(250),
    status      varchar(5),
    create_at   timestamp default now(),
    create_by   varchar(10),
    update_at   timestamp,
    update_by   varchar(10)
);

alter table public.idm_role
    owner to phillip;

create table if not exists public.idm_role_attr
(
    id        serial
        constraint "IDM_ROLE_ATTR_PK"
            primary key,
    role_id   integer not null,
    app_code  varchar(10),
    name      varchar(100),
    key       varchar(20),
    value     varchar(250),
    status    varchar(5),
    create_at timestamp default now(),
    create_by varchar(10),
    update_at timestamp,
    update_by varchar(10)
);

alter table public.idm_role_attr
    owner to phillip;

create table if not exists public.idm_role_doa_authority
(
    id                 serial
        constraint "INS_ROLE_DOA_AUTHORITY_PK"
            primary key,
    role_id            integer,
    ccy                text,
    amount_slab        double precision,
    allow_discount     varchar(1),
    discount_percent   double precision,
    lower_amt_approval varchar(1),
    quote_approval     varchar(1),
    policy_approval    varchar(1),
    status             varchar(3),
    created_at         timestamp default now(),
    created_by         varchar(10),
    updated_at         timestamp,
    updated_by         varchar(10)
);

alter table public.idm_role_doa_authority
    owner to phillip;

create table if not exists public.idm_role_func
(
    id          serial
        constraint "IDM_ROLE_FUNC_PK"
            primary key,
    role_id     integer not null,
    function_id integer not null,
    app_code    varchar(10),
    permission  varchar(250),
    status      varchar(5),
    create_at   timestamp default now(),
    create_by   varchar(10),
    update_at   timestamp,
    update_by   varchar(10)
);

alter table public.idm_role_func
    owner to phillip;

create table if not exists public.idm_token
(
    id            serial
        constraint "IDM_TOKEN_PK"
            primary key,
    refresh_token varchar(100),
    expires_on    timestamp,
    client_id     integer,
    user_id       integer,
    device_id     varchar(100)
);

alter table public.idm_token
    owner to phillip;

create table if not exists public.idm_user
(
    id                     serial
        constraint "IDM_USER_PK"
            primary key,
    user_uuid              varchar(100),
    username               varchar(50),
    full_name              varchar(100),
    email                  varchar(100),
    employee_id            varchar(5),
    password               varchar(250),
    grant_type             varchar(50),
    status                 varchar(5),
    access_lvl             integer,
    force_change_pwd       varchar(1),
    password_expire_at     timestamp,
    invalid_password_count integer,
    invalid_password_at    timestamp,
    two_fa_auth            varchar(1),
    create_at              timestamp default now(),
    create_by              varchar(200),
    update_at              timestamp,
    update_by              varchar(200),
    type                   varchar(50),
    position               text,
    phone_no               text,
    department             text
);

alter table public.idm_user
    owner to phillip;

create table if not exists public.idm_user_admin_pin
(
    id            serial
        constraint "IDM_USER_ADMIN_PIN_PK"
            primary key,
    user_id       integer not null,
    pin           varchar(250),
    pin_expire_at timestamp,
    status        varchar(5),
    create_at     timestamp default now(),
    create_by     varchar(10),
    update_at     timestamp,
    update_by     varchar(10)
);

alter table public.idm_user_admin_pin
    owner to phillip;

create table if not exists public.idm_user_application
(
    id            serial
        constraint "IDM_USER_APPLICATION_PK"
            primary key,
    user_id       integer,
    username      varchar(100),
    app_code      varchar(100),
    status        varchar(10),
    last_login_at timestamp,
    create_at     timestamp default now(),
    create_by     varchar(100),
    update_at     timestamp,
    update_by     varchar(100)
);

alter table public.idm_user_application
    owner to phillip;

create table if not exists public.idm_user_attr
(
    id        serial
        constraint "IDM_USER_ATTR_PK"
            primary key,
    user_id   integer not null,
    app_code  varchar(10),
    name      varchar(100),
    key       varchar(20),
    value     varchar(250),
    status    varchar(5),
    create_at timestamp default now(),
    create_by varchar(10),
    update_at timestamp,
    update_by varchar(10)
);

alter table public.idm_user_attr
    owner to phillip;

create table if not exists public.idm_user_branch
(
    id          serial
        constraint "IDM_USER_BRANCH_PK"
            primary key,
    user_id     integer not null,
    username    varchar(100),
    branch_code varchar(100),
    app_code    varchar(10),
    home_branch varchar(5),
    status      varchar(10),
    create_at   timestamp default now(),
    create_by   varchar(100),
    update_at   timestamp,
    update_by   varchar(100)
);

alter table public.idm_user_branch
    owner to phillip;

create table if not exists public.idm_user_detail
(
    id                     serial
        constraint "IDM_USER_DETAIL_PK"
            primary key,
    user_id                integer not null,
    access_lvl             integer,
    full_name              varchar(100),
    status                 varchar(5),
    last_login_at          timestamp,
    force_change_pwd       integer,
    password_expire_at     timestamp,
    invalid_password_count integer,
    two_fa_auth            integer,
    auth_type              varchar(10)
);

alter table public.idm_user_detail
    owner to phillip;

create table if not exists public.idm_user_file_storage
(
    id             serial
        constraint "IDM_USER_FILE_STORAGE_PK"
            primary key,
    user_id        integer,
    file_type      varchar(50),
    file_name      varchar(50),
    storage_option varchar(25),
    file_url       varchar(250),
    file_base64    text,
    note           varchar(500),
    status         varchar(3),
    created_at     timestamp default now(),
    created_by     varchar(10),
    updated_at     timestamp,
    updated_by     varchar(10)
);

alter table public.idm_user_file_storage
    owner to phillip;

create table if not exists public.idm_user_func
(
    id          serial
        constraint "IDM_USER_FUNC_PK"
            primary key,
    user_id     integer not null,
    function_id integer not null,
    app_code    varchar(10),
    permission  varchar(50),
    status      varchar(5),
    create_at   timestamp default now(),
    create_by   varchar(10),
    update_at   timestamp,
    update_by   varchar(10)
);

alter table public.idm_user_func
    owner to phillip;

create table if not exists public.idm_user_group
(
    id        serial
        constraint "IDM_USER_GROUP_PK"
            primary key,
    username  varchar(250),
    user_id   integer not null,
    group_id  integer not null,
    status    varchar(5),
    create_at timestamp default now(),
    create_by varchar(10),
    update_at timestamp,
    update_by varchar(10)
);

alter table public.idm_user_group
    owner to phillip;

create table if not exists public.idm_user_log
(
    id          serial
        constraint "IDM_USER_LOG_PK"
            primary key,
    user_id     integer not null,
    username    varchar(100),
    app_code    varchar(10),
    login_at    timestamp,
    ip_address  varchar(100),
    agent       varchar(200),
    description varchar(250),
    status      varchar(5),
    create_at   timestamp default now()
);

alter table public.idm_user_log
    owner to phillip;

create table if not exists public.idm_user_role
(
    id        serial
        constraint "IDM_USER_ROLE_PK"
            primary key,
    user_id   integer not null,
    role_id   integer not null,
    app_code  varchar(10),
    status    varchar(10),
    create_at timestamp default now(),
    create_by varchar(10),
    update_at timestamp,
    update_by varchar(10)
);

alter table public.idm_user_role
    owner to phillip;

create table if not exists public.idm_validity
(
    id          serial
        constraint "IDM_VALIDITY_PK"
            primary key,
    user_id     integer not null,
    group_id    integer not null,
    role_id     integer not null,
    function_id integer not null,
    branch_code varchar(5),
    app_code    varchar(10),
    permission  varchar(10),
    description varchar(250),
    allow       varchar(1),
    valid_at    timestamp,
    expire_at   timestamp,
    status      varchar(5),
    create_at   timestamp default now(),
    create_by   varchar(20),
    update_at   timestamp,
    update_by   varchar(20)
);

alter table public.idm_validity
    owner to phillip;

create table if not exists public.ins_account_personnel
(
    id               serial
        constraint "INS_ACCOUNT_PERSONNEL_PK"
            primary key,
    code             varchar(25),
    short_name       varchar(100),
    full_name        varchar(100),
    business_channel varchar(100),
    alt_code         varchar(25),
    status           varchar(5),
    created_at       timestamp default now(),
    created_by       varchar(10),
    updated_at       timestamp,
    updated_by       varchar(10)
);

alter table public.ins_account_personnel
    owner to phillip;

create table if not exists public.ins_address
(
    pin_code      varchar(8) not null
        constraint "INS_ADDRESS_PK"
            primary key,
    address_code  varchar(225),
    address_line1 varchar(225),
    address_line2 varchar(225),
    address_line3 varchar(225),
    address_line4 varchar(225),
    country       varchar(50),
    status        varchar(3),
    created_at    timestamp default now(),
    created_by    varchar(10),
    updated_at    timestamp,
    updated_by    varchar(10)
);

alter table public.ins_address
    owner to phillip;

create table if not exists public.ins_address_code
(
    id          serial
        constraint "INS_ADDRESS_CODE_PK"
            primary key,
    postal_code text       not null,
    province    varchar,
    province_kh text,
    province_zh text,
    district    varchar,
    district_kh text,
    district_zh text,
    commune     varchar,
    commune_kh  text,
    commune_zh  text,
    status      varchar(3) not null,
    created_at  timestamp  not null,
    created_by  text,
    updated_at  timestamp,
    updated_by  text
);

alter table public.ins_address_code
    owner to phillip;

create table if not exists public.ins_auto_data_clause
(
    id         serial
        constraint ins_auto_data_clause_pk
            primary key,
    data_id    integer,
    clause_id  integer,
    status     varchar(3),
    created_at timestamp default now(),
    created_by varchar(10),
    updated_at timestamp,
    updated_by varchar(10)
);

alter table public.ins_auto_data_clause
    owner to phillip;

create table if not exists public.ins_auto_data_detail
(
    id                      serial
        constraint ins_auto_data_detail_pk
            primary key,
    product_code            varchar(5),
    master_data_type        varchar(25),
    master_data_id          integer,
    model_id                integer,
    plate_no                varchar(25),
    chassis_no              varchar(25),
    engine_no               varchar(25),
    manufacturing_year      integer,
    cubic                   text,
    vehicle_value           double precision,
    passenger               double precision,
    tonnage                 double precision,
    surcharge               double precision,
    discount                double precision,
    ncd                     double precision,
    selected_cover_pkg      varchar(500),
    negotiation_rate        double precision,
    premium                 double precision,
    status                  varchar(3),
    created_at              timestamp default now(),
    created_by              varchar(10),
    updated_at              timestamp,
    updated_by              varchar(10),
    remark                  varchar(500),
    commercial_vehicle_type varchar(50),
    cover_pkg_id            integer,
    endorsement_stage       text,
    endorsement_state       text,
    previous_id             integer,
    inception_date          date,
    refund_option           text,
    refund_percentage       double precision,
    premium_amt_bf_refund   double precision,
    endorsement_e_date      date,
    endos_day_remaining     integer,
    custom_refund_amount    double precision,
    vehicle_usage           text,
    claim_request_count     integer,
    claim_incurred          double precision,
    claim_paid              double precision,
    claim_outstanding       double precision,
    claim_ratio             double precision,
    vehicle_uuid            text
);

alter table public.ins_auto_data_detail
    owner to phillip;

create table if not exists public.ins_auto_data_detail_temp
(
    id                      serial
        constraint ins_auto_data_detail_pk_2
            primary key,
    product_code            varchar(5),
    master_data_type        varchar(25),
    master_data_id          integer,
    model_id                integer,
    plate_no                varchar(25),
    chassis_no              varchar(25),
    engine_no               varchar(25),
    manufacturing_year      integer,
    cubic                   text,
    vehicle_value           double precision,
    passenger               double precision,
    tonnage                 double precision,
    surcharge               double precision,
    discount                double precision,
    ncd                     double precision,
    selected_cover_pkg      varchar(500),
    negotiation_rate        double precision,
    premium                 double precision,
    status                  varchar(3),
    created_at              timestamp default now(),
    created_by              varchar(10),
    updated_at              timestamp,
    updated_by              varchar(10),
    remark                  varchar(500),
    commercial_vehicle_type varchar(50),
    cover_pkg_id            integer,
    endorsement_stage       text,
    endorsement_state       text,
    previous_id             integer,
    inception_date          date,
    refund_option           text,
    refund_percentage       double precision,
    premium_amt_bf_refund   double precision,
    endorsement_e_date      date,
    endos_day_remaining     integer,
    custom_refund_amount    double precision,
    vehicle_usage           text
);

alter table public.ins_auto_data_detail_temp
    owner to phillip;

create table if not exists public.ins_auto_data_master
(
    id                     serial
        constraint ins_auto_data_master_pk
            primary key,
    data_type              varchar(25),
    product_code           varchar(5),
    branch_code            varchar(3),
    customer_no            varchar(9),
    calc_option            varchar(50),
    insurance_period_type  varchar(50),
    insurance_period_code  varchar(50),
    insurance_period_val   double precision,
    sum_insured            double precision,
    total_premium          double precision,
    status                 varchar(3),
    created_at             timestamp default now(),
    created_by             varchar(10),
    updated_at             timestamp,
    updated_by             varchar(10),
    negotiation_rate       double precision,
    remark                 varchar(500),
    joint_status           varchar(1),
    insured_name           text,
    business_code          varchar(20),
    sale_channel           varchar(50),
    commission_rate        double precision,
    handler_code           varchar(20),
    warranty               text,
    memorandum             text,
    subjectivity           text,
    insured_name_kh        text,
    insured_name_zh        text,
    policy_wording_version varchar,
    previous_id            integer,
    effective_date_from    date,
    effective_date_to      date,
    effective_month        double precision,
    effective_day          double precision,
    endorsement_e_date     date,
    endos_day_remaining    integer,
    endorsement_type       text,
    warranty_kh            text,
    memorandum_kh          text,
    subjectivity_kh        text
);

alter table public.ins_auto_data_master
    owner to phillip;

create table if not exists public.ins_lov_vehicle_make
(
    id                serial
        constraint ins_lov_vehicle_make_pk
            primary key,
    make              varchar(255) not null,
    description       varchar(255),
    available_offline varchar(1)   not null,
    available_online  varchar(1)   not null,
    status            varchar(3)   not null,
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10)
);

alter table public.ins_lov_vehicle_make
    owner to phillip;

create table if not exists public.ins_lov_vehicle_model
(
    id                serial
        constraint ins_lov_vehicle_model_pk
            primary key,
    make_id           integer,
    product_code      varchar(25),
    model             varchar(100),
    vehicle_type      varchar(100),
    classification    varchar(100),
    available_offline varchar(1) not null,
    available_online  varchar(1),
    commercial_model  varchar(100),
    status            varchar(3),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10)
);

alter table public.ins_lov_vehicle_model
    owner to phillip;

create table if not exists public.ins_policy
(
    id                      serial
        constraint "INS_POLICY_PK"
            primary key,
    policy_no               varchar(25),
    version                 integer,
    cycle                   integer,
    document_no             varchar(25),
    quotation_id            integer,
    branch_code             varchar(3),
    customer_no             varchar(20),
    product_line_code       varchar(25),
    product_code            varchar(5),
    data_id                 integer,
    sum_insured             double precision,
    premium                 double precision,
    policy_alt_no           varchar(50),
    account_code            varchar(6),
    handler_code            varchar(6),
    status                  varchar(5),
    created_at              timestamp default now(),
    created_by              varchar(10),
    updated_at              timestamp,
    updated_by              varchar(10),
    approved_at             timestamp,
    approved_by             varchar(10),
    business_type           text,
    policy_type             text,
    approved_status         varchar(3),
    approved_reason         text,
    endorsement_description text,
    request_amount          double precision,
    renewal_reference_id    integer,
    reference_source        varchar,
    reference_id            integer,
    referral_code           varchar,
    constraint ins_policy_ref_source_ref_id_unq
        unique (reference_source, reference_id)
);

alter table public.ins_policy
    owner to phillip;

create index if not exists ins_policy_idx2
    on public.ins_policy (data_id);

create table if not exists public.ins_quotation
(
    id                serial
        constraint "INS_QUOTATION_PK"
            primary key,
    quotation_no      varchar(25),
    version           integer,
    branch_code       varchar(3),
    customer_no       varchar(20),
    product_line_code varchar(25),
    product_code      varchar(5),
    data_id           integer,
    sum_insured       double precision,
    premium           double precision,
    quotation_alt_no  varchar(50),
    status            varchar(5),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10),
    account_code      varchar(6),
    handler_code      varchar(6),
    document_no       varchar(50),
    approved_at       timestamp,
    approved_by       varchar(10),
    approved_status   varchar(5),
    accepted_status   varchar(5),
    accepted_at       timestamp,
    accepted_by       varchar(10),
    accepted_reason   text,
    approved_reason   text
);

alter table public.ins_quotation
    owner to phillip;

create table if not exists public.ins_product
(
    id                                serial
        constraint "INS_PRODUCT_PK"
            primary key,
    code                              varchar(5),
    name                              varchar(50),
    description                       varchar(500),
    product_line_code                 varchar(25),
    alt_code                          varchar(50),
    status                            varchar(3),
    created_at                        timestamp default now(),
    created_by                        varchar(10),
    updated_at                        timestamp,
    updated_by                        varchar(10),
    renewable                         varchar(1),
    name_kh                           text,
    name_zh                           text,
    description_kh                    text,
    description_zh                    text,
    group_code                        text,
    default_surcharge                 double precision,
    default_discount                  double precision,
    default_ncd                       double precision,
    limitation_to_use_en              text,
    limitation_to_use_kh              text,
    limitation_to_use_zh              text,
    prod_specification                text,
    vehicle_type                      text,
    vehicle_type_kh                   text,
    vehicle_type_zh                   text,
    schema_standard_code              varchar,
    schema_optional_code              varchar,
    coverage_en                       text,
    coverage_kh                       text,
    coverage_zh                       text,
    default_accumulation_limit_amount numeric   default 0 not null
);

alter table public.ins_product
    owner to phillip;

create table if not exists public.ins_business_channel
(
    id                   serial
        constraint "INS_BUSINESS_CHANNEL_PK"
            primary key,
    business_category_id integer,
    business_code        varchar(20),
    full_name            varchar(500),
    sale_channel         varchar(50),
    commission_rate      double precision,
    handler_code         varchar(20),
    contact_phone        varchar(500),
    contact_email        varchar(500),
    contact_address      text,
    parent_code          varchar(20),
    status               varchar(5),
    created_at           timestamp default now(),
    created_by           varchar(10),
    updated_at           timestamp,
    updated_by           varchar(10),
    premium_tax_fee_rate double precision,
    witholding_tax_rate  double precision
);

alter table public.ins_business_channel
    owner to phillip;

create table if not exists public.ins_country
(
    id               serial
        constraint "INS_COUNTRY_PK"
            primary key,
    country_code     text,
    description      text,
    alt_country_code text,
    isd_code         text,
    status           varchar(3),
    created_at       timestamp default now(),
    created_by       varchar(10),
    updated_at       timestamp,
    updated_by       varchar(10)
);

alter table public.ins_country
    owner to phillip;

create table if not exists public.ins_cust_contact
(
    id            serial
        constraint ins_cust_contact_pk
            primary key,
    customer_no   varchar(20),
    contact_level varchar(50),
    contact_type  varchar(50),
    contact_info  varchar(255),
    status        varchar(5) default 'ACT'::character varying not null,
    created_at    timestamp  default now(),
    created_by    varchar(10),
    updated_at    timestamp,
    updated_by    varchar(10)
);

alter table public.ins_cust_contact
    owner to phillip;

create table if not exists public.ins_cust_corporate
(
    id                       serial
        constraint "INS_CUST_CORPORATE_PK"
            primary key,
    customer_no              varchar(20),
    corporate_name           varchar(100),
    incorporate_date         date,
    business_description     varchar(50),
    identity_type            varchar(25) default 'REGISTER_NO'::character varying,
    identity_no              varchar(25),
    identity_iss_date        date,
    identity_exp_date        date,
    telephone1               varchar(25),
    telephone2               varchar(25),
    telephone3               varchar(25),
    r_address1               varchar(100),
    r_address2               varchar(100),
    r_address3               varchar(100),
    r_address4               varchar(100),
    status                   varchar(5),
    created_at               timestamp   default now(),
    created_by               varchar(10),
    updated_at               timestamp,
    updated_by               varchar(10),
    tin_code                 varchar(255),
    foreign_tin_no           varchar(255),
    company_name_en          varchar(255),
    company_name_kh          varchar(255),
    address                  text,
    business_registration_no varchar(255),
    vat_tin                  varchar(255)
);

alter table public.ins_cust_corporate
    owner to phillip;

create table if not exists public.ins_cust_individual
(
    id                serial
        constraint "INS_CUST_INDIVIDUAL_PK"
            primary key,
    customer_no       varchar(20),
    first_name        varchar(25),
    middle_name       varchar(25),
    last_name         varchar(25),
    customer_prefix   varchar(10),
    date_of_birth     date,
    place_of_birth    varchar(250),
    gender            varchar(1),
    identity_type     varchar(25),
    identity_no       varchar(25),
    identity_iss_date date,
    identity_exp_date date,
    nationality_code  varchar(2),
    country_code      varchar(10),
    telephone1        varchar(25),
    telephone2        varchar(25),
    telephone3        varchar(25),
    mobile_phone1     varchar(25),
    mobile_phone2     varchar(25),
    mobile_phone3     varchar(25),
    resident_status   varchar(1) default 'R'::character varying,
    d_address1        varchar(100),
    d_address2        varchar(100),
    d_address3        varchar(100),
    d_address4        varchar(100),
    p_address1        varchar(100),
    p_address2        varchar(100),
    p_address3        varchar(100),
    p_address4        varchar(100),
    status            varchar(5) default 'ACT'::character varying not null,
    created_at        timestamp  default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10),
    company_name_kh   varchar(100),
    company_name_en   varchar(100),
    national          varchar(50),
    nationality       varchar(50)
);

alter table public.ins_cust_individual
    owner to phillip;

create table if not exists public.ins_customer
(
    id                  serial
        constraint "INS_CUSTOMER_PK"
            primary key,
    broker_id           integer,
    customer_no         varchar(20),
    branch_code         varchar(3),
    customer_type       varchar(20),
    full_name           varchar(100),
    language_code       varchar(2),
    cust_classification varchar(20),
    risk_category       varchar(10),
    status              varchar(5) default 'ACT'::character varying not null,
    created_at          timestamp  default now(),
    created_by          varchar(10),
    updated_at          timestamp,
    updated_by          varchar(10),
    name_en             text,
    name_kh             text,
    address_en          text,
    address_kh          text,
    country_code        text,
    village_en          varchar,
    village_kh          text,
    postal_code         text,
    reference_source    varchar,
    reference_id        integer,
    constraint ins_customer_ref_source_ref_id_unq
        unique (reference_source, reference_id)
);

alter table public.ins_customer
    owner to phillip;

create table if not exists public.ins_policy_commission_data
(
    id                    serial
        constraint "INS_POLICY_COMMISSION_DATA_PK"
            primary key,
    policy_id             integer,
    policy_no             text,
    business_category     text,
    business_code         text,
    gross_written_premium double precision,
    premium_tax_fee_rate  double precision,
    premium_tax_fee       double precision,
    net_written_premium   double precision,
    commission_rate       double precision,
    commission_amount     double precision,
    witholding_tax_rate   double precision,
    witholding_tax        double precision,
    commission_due_amount double precision,
    status                varchar(3),
    created_at            timestamp default now(),
    created_by            varchar(10),
    updated_at            timestamp,
    updated_by            varchar(10),
    data_id               integer,
    detail_id             integer
);

alter table public.ins_policy_commission_data
    owner to phillip;

create table if not exists public.ins_policy_invoice_note
(
    id                 serial
        constraint "INS_POLICY_INVOICE_NOTE_PK"
            primary key,
    policy_id          integer          not null,
    policy_document_no varchar(20)      not null,
    type               varchar(12)      not null,
    inv_cdn_no         varchar(15)      not null,
    seq_no             varchar(10)      not null,
    issue_date         date             not null,
    code               varchar(10)      not null,
    customer_name      text,
    address            text,
    tin_code           varchar(10),
    product_name       varchar(50),
    insurance_period   text,
    endorsement_e_date date,
    total_premuim      double precision not null,
    exch_rate          double precision not null,
    status             varchar(3)       not null,
    created_at         timestamp        not null,
    created_by         text,
    updated_at         timestamp,
    updated_by         text
);

alter table public.ins_policy_invoice_note
    owner to phillip;

create table if not exists public.ins_ref_enum
(
    id           serial,
    enum_id      varchar(50),
    name         varchar(100),
    description  varchar(500),
    seq_no       integer,
    table_name   varchar(50),
    type_id      varchar(50),
    group_id     varchar(50),
    system_level varchar(1),
    status       varchar(3),
    created_at   timestamp default now(),
    created_by   varchar(10),
    updated_at   timestamp,
    updated_by   varchar(10)
);

alter table public.ins_ref_enum
    owner to phillip;

create table if not exists public.ins_reinsurance_data
(
    id                serial
        constraint "INS_REINSURANCE_DATA_PK"
            primary key,
    policy_id         integer,
    data_id           integer,
    detail_id         integer,
    product_line_code varchar(25),
    product_code      varchar(5),
    uw_year           integer,
    treaty_code       varchar(50),
    lvl               integer,
    parent_code       varchar(50),
    share             double precision,
    sum_insured       double precision,
    premium           double precision,
    ri_commission     double precision,
    ri_commission_amt double precision,
    tax_fee           double precision,
    tax_fee_amt       double precision,
    net_premium       double precision,
    endorsement_stage text,
    endorsement_state text,
    status            varchar(5),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10)
);

alter table public.ins_reinsurance_data
    owner to phillip;

create index if not exists ins_reinsurance_data_detail_id_idx
    on public.ins_reinsurance_data (detail_id, treaty_code, lvl, endorsement_stage, status);

create table if not exists public.ins_reinsurance_partner
(
    id          serial
        constraint "INS_REINSURANCE_PARTNER_PK"
            primary key,
    code        text,
    name        text,
    description text,
    status      varchar(3),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10),
    group_code  text
);

alter table public.ins_reinsurance_partner
    owner to phillip;

create table if not exists public.ins_branch
(
    id           serial
        constraint "INS_BRANCH_PK"
            primary key,
    code         varchar(3),
    name         varchar(50),
    region_code  varchar(10),
    country_code varchar(2),
    branch_addr1 varchar(250),
    branch_addr2 varchar(250),
    branch_addr3 varchar(250),
    branch_lcy   varchar(3),
    cut_off_time varchar(20),
    status       varchar(3),
    created_at   timestamp default now(),
    created_by   varchar(10),
    updated_at   timestamp,
    updated_by   varchar(10)
);

alter table public.ins_branch
    owner to phillip;

create table if not exists public.ins_business_category
(
    id                   serial
        constraint "INS_BUSINESS_CATEGORY_PK"
            primary key,
    name                 varchar(100),
    description          varchar(500),
    prefix               varchar(10),
    status               varchar(5),
    created_at           timestamp default now(),
    created_by           varchar(10),
    updated_at           timestamp,
    updated_by           varchar(10),
    premium_tax_fee_rate double precision,
    witholding_tax_rate  double precision
);

alter table public.ins_business_category
    owner to phillip;

create table if not exists public.ins_business_handler
(
    id             serial
        constraint "INS_BUSINESS_HANDLER_PK"
            primary key,
    handler_code   varchar(4),
    title          varchar(25),
    name           varchar(100),
    employee_code  varchar(50),
    phone          varchar(500),
    email          varchar(500),
    incentive_rate double precision,
    status         varchar(5),
    created_at     timestamp default now(),
    created_by     varchar(10),
    updated_at     timestamp,
    updated_by     varchar(10)
);

alter table public.ins_business_handler
    owner to phillip;

create table if not exists public.ins_claim
(
    id                       serial
        constraint ins_claim_pk
            primary key,
    policy_id                integer                 not null,
    data_id                  integer                 not null,
    detail_id                integer                 not null,
    third_party_id           integer,
    seq_no                   varchar                 not null,
    claim_no                 varchar(20)             not null,
    collision_by             varchar,
    notification_date        date,
    incident_date            date                    not null,
    incident_location        text                    not null,
    latitude                 double precision,
    longitude                double precision,
    claim_type               varchar,
    processing_month         date      default now() not null,
    insured_period_from      date                    not null,
    insured_period_to        date                    not null,
    status                   varchar(3)              not null,
    created_at               timestamp default now() not null,
    created_by               integer                 not null,
    updated_at               timestamp,
    updated_by               text,
    approved_by              text,
    approved_cmt             text,
    approved_status          varchar(5),
    approved_at              timestamp,
    driver_id                integer                 not null,
    confirmed_final_claim    varchar   default 'N'::character varying,
    confirmed_final_claim_at timestamp,
    remark                   text,
    adjuster_company_id      integer,
    updated_final_at         timestamp,
    updated_final_by         text,
    vehicle_uuid             uuid
);

alter table public.ins_claim
    owner to phillip;

create table if not exists public.ins_claim_adjuster_company
(
    id           serial,
    name_kh      text,
    name_en      varchar,
    postal_code  varchar,
    home_no      varchar,
    street_no    varchar,
    commune      varchar,
    district     varchar,
    city         varchar,
    phone_number varchar,
    email        varchar,
    address      text,
    description  text,
    status       varchar(3)              not null,
    created_at   timestamp default now() not null,
    created_by   text                    not null,
    updated_at   timestamp,
    updated_by   text
);

alter table public.ins_claim_adjuster_company
    owner to phillip;

create table if not exists public.ins_claim_cause_of_loss
(
    id                serial,
    cause_name        varchar(100)            not null,
    code              varchar,
    status            varchar(3)              not null,
    created_at        timestamp default now() not null,
    created_by        text                    not null,
    updated_at        timestamp,
    updated_by        text,
    product_code      varchar(5),
    product_line_code varchar(25),
    alt_code          varchar(50),
    cause_name_kh     text
);

alter table public.ins_claim_cause_of_loss
    owner to phillip;

create table if not exists public.ins_claim_detail
(
    id                        integer          default nextval('ins_claim_component_id_seq'::regclass) not null,
    policy_id                 integer                                                                  not null,
    data_id                   integer                                                                  not null,
    claim_no                  varchar                                                                  not null,
    cause_of_loss_desc        varchar,
    cause_of_loss_code        varchar                                                                  not null,
    type                      varchar                                                                  not null,
    amount                    double precision                                                         not null,
    status                    varchar(3)                                                               not null,
    created_at                timestamp        default now()                                           not null,
    created_by                text                                                                     not null,
    updated_at                timestamp,
    updated_by                text,
    detail_id                 integer                                                                  not null,
    claim_id                  integer                                                                  not null,
    recovery_from_third_party double precision default 0,
    vehicle_uuid              uuid
);

alter table public.ins_claim_detail
    owner to phillip;

create index if not exists ins_claim_detail_idx2
    on public.ins_claim_detail (claim_no, policy_id, detail_id, cause_of_loss_code);

create table if not exists public.ins_claim_document
(
    id          serial,
    policy_id   integer                 not null,
    claim_no    varchar,
    data_id     integer,
    detail_id   integer,
    name        varchar,
    type        varchar,
    file_path   varchar,
    media_type  varchar,
    description varchar,
    status      varchar(3)              not null,
    created_at  timestamp default now() not null,
    created_by  text                    not null,
    updated_at  timestamp,
    updated_by  text
);

alter table public.ins_claim_document
    owner to phillip;

create table if not exists public.ins_claim_driver_license
(
    id                  integer   default nextval('ins_driver_license_id_seq'::regclass) not null
        constraint ins_driver_license_pk
            primary key,
    name                text                                                             not null,
    gender              varchar(1)                                                       not null,
    license_no          varchar                                                          not null,
    license_issue_date  date                                                             not null,
    license_expire_date date                                                             not null,
    driver_age          integer                                                          not null,
    occupation          varchar(100),
    status              varchar(3)                                                       not null,
    created_at          timestamp default now()                                          not null,
    created_by          text                                                             not null,
    updated_at          timestamp,
    updated_by          text,
    postal_code         varchar,
    home_no             varchar,
    street_no           varchar,
    commune             varchar,
    district            varchar,
    city                varchar,
    phone_number        varchar,
    email               varchar,
    address             text,
    description         text
);

alter table public.ins_claim_driver_license
    owner to phillip;

create table if not exists public.ins_claim_generate_payment_or_claim_no
(
    id                 serial
        constraint ins_claim_generate_payment_no_pk
            primary key,
    detail_id          integer               not null,
    seq_no             varchar               not null,
    payment_no         varchar               not null,
    claim_no           varchar               not null,
    year               varchar default now() not null,
    generate_type      varchar               not null,
    cause_of_loss_code varchar,
    ms_tbl_handler     varchar               not null,
    vehicle_uuid       uuid,
    constraint ins_claim_generate_payment_or_seq_no_year_generate_type_ms__key
        unique (seq_no, year, generate_type, ms_tbl_handler)
);

alter table public.ins_claim_generate_payment_or_claim_no
    owner to phillip;

create table if not exists public.ins_claim_service_provider
(
    id            integer   default nextval('ins_claim_occupation_group_id_seq'::regclass) not null
        constraint iins_claim_partner_pk
            primary key,
    name          text                                                                     not null,
    email         varchar(100),
    phone_number  varchar(100),
    home_no       varchar(4),
    street_no     varchar,
    commune       varchar(50),
    district      varchar(50),
    city          varchar(50),
    business_logo varchar(200),
    latitude      double precision,
    longitude     double precision,
    type          varchar,
    is_partner    varchar,
    status        varchar(3)                                                               not null,
    created_at    timestamp default now()                                                  not null,
    created_by    text                                                                     not null,
    updated_at    timestamp,
    updated_by    text,
    payee_id      integer
);

alter table public.ins_claim_service_provider
    owner to phillip;

create table if not exists public.ins_claim_payee
(
    id           serial,
    name_en      text                    not null,
    type         varchar                 not null,
    postal_code  varchar,
    home_no      varchar,
    street_no    varchar,
    commune      varchar,
    district     varchar,
    city         varchar,
    phone_number varchar,
    email        varchar,
    address      text,
    description  text,
    status       varchar(3)              not null,
    created_at   timestamp default now() not null,
    created_by   text                    not null,
    updated_at   timestamp,
    updated_by   text,
    name_kh      text
);

alter table public.ins_claim_payee
    owner to phillip;

create table if not exists public.ins_claim_payment
(
    id                         serial
        constraint ins_claim_payment_pk
            primary key,
    policy_id                  integer                 not null,
    data_id                    integer                 not null,
    detail_id                  integer                 not null,
    claim_no                   varchar(20)             not null,
    total_amount               double precision        not null,
    status                     varchar(3)              not null,
    created_at                 timestamp default now() not null,
    created_by                 integer                 not null,
    updated_at                 timestamp,
    updated_by                 text,
    approved_status            varchar(5),
    approved_at                timestamp,
    approved_by                text,
    approved_cmt               text,
    confirmed_final_payment    varchar   default 'N'::character varying,
    confirmed_final_payment_at timestamp,
    updated_final_at           timestamp,
    updated_final_by           text,
    vehicle_uuid               uuid
);

alter table public.ins_claim_payment
    owner to phillip;

create index if not exists ins_claim_payment_idx2
    on public.ins_claim_payment (policy_id, detail_id);

create table if not exists public.ins_claim_payment_detail
(
    id                 serial,
    policy_id          integer                 not null,
    data_id            integer                 not null,
    detail_id          integer                 not null,
    claim_no           varchar                 not null,
    cause_of_loss_code varchar                 not null,
    amount             double precision        not null,
    remark             text,
    status             varchar(3)              not null,
    created_at         timestamp default now() not null,
    created_by         integer                 not null,
    updated_at         timestamp,
    updated_by         text,
    payment_id         integer                 not null,
    payee_id           integer,
    payment_no         varchar,
    payee_address      text,
    payment_type       varchar,
    vehicle_uuid       uuid
);

alter table public.ins_claim_payment_detail
    owner to phillip;

create table if not exists public.ins_claim_third_party
(
    id                 serial,
    name_kh            text,
    identity_card      integer,
    home_no            varchar,
    street_no          varchar,
    commune            varchar,
    district           varchar,
    city               varchar,
    vehicle_model      varchar,
    plate_no           varchar,
    engine_no          varchar,
    manufacturing_year integer,
    email              varchar,
    phone_number       varchar,
    address            text,
    description        text,
    status             varchar(3)              not null,
    created_at         timestamp default now() not null,
    created_by         text                    not null,
    updated_at         timestamp,
    updated_by         text,
    vehicle_make       varchar,
    name_en            varchar,
    license_no         varchar
);

alter table public.ins_claim_third_party
    owner to phillip;

create table if not exists public.ins_claim_transaction
(
    id                         serial
        constraint ins_claim_transaction_pk
            primary key,
    policy_id                  integer                 not null,
    data_id                    integer                 not null,
    detail_id                  integer                 not null,
    claim_no                   varchar                 not null,
    claim_payable              double precision        not null,
    claim_request              double precision        not null,
    total_claim                double precision        not null,
    status                     varchar(3)              not null,
    created_at                 timestamp default now() not null,
    created_by                 integer                 not null,
    updated_at                 timestamp,
    updated_by                 text,
    approved_status            varchar(5),
    approved_at                timestamp,
    approved_by                text,
    approved_cmt               text,
    confirmed_final_process    varchar   default 'N'::character varying,
    confirmed_final_process_at timestamp,
    product_code               varchar                 not null,
    cond_type                  varchar   default 'PROCESS'::character varying,
    updated_final_at           timestamp,
    updated_final_by           text,
    vehicle_uuid               uuid
);

alter table public.ins_claim_transaction
    owner to phillip;

create index if not exists ins_claim_transaction_idx2
    on public.ins_claim_transaction (policy_id, detail_id);

create table if not exists public.ins_claim_transaction_detail
(
    id                        serial,
    policy_id                 integer                        not null,
    data_id                   integer                        not null,
    detail_id                 integer                        not null,
    payee_id                  integer                        not null,
    claim_no                  varchar                        not null,
    cause_of_loss_code        varchar                        not null,
    type                      varchar                        not null,
    cond_type                 varchar                        not null,
    payee_address             varchar,
    insured_sharing_request   double precision               not null,
    partial_amount            double precision               not null,
    remain_amount             double precision               not null,
    deductible                double precision               not null,
    salvage                   double precision default 0,
    third_party_recovery      double precision,
    cond_value_type           varchar,
    remark                    text,
    status                    varchar(3)                     not null,
    created_at                timestamp        default now() not null,
    created_by                integer                        not null,
    updated_at                timestamp,
    updated_by                text,
    txn_id                    integer                        not null,
    payment_no                varchar,
    deductible_paid           double precision,
    payment_type              varchar,
    recovery_from_third_party double precision default 0,
    vehicle_uuid              uuid
);

alter table public.ins_claim_transaction_detail
    owner to phillip;

create table if not exists public.ins_claim_transaction_detail_temp
(
    id                      serial,
    policy_id               integer                 not null,
    data_id                 integer                 not null,
    detail_id               integer                 not null,
    payee_id                integer                 not null,
    claim_no                varchar                 not null,
    payment_no              varchar,
    cause_of_loss_code      varchar                 not null,
    type                    varchar                 not null,
    payment_type            varchar,
    cond_type               varchar                 not null,
    cond_value_type         varchar,
    payee_address           varchar,
    insured_sharing_request double precision        not null,
    partial_amount          double precision        not null,
    remain_amount           double precision        not null,
    deductible              double precision        not null,
    deductible_paid         double precision,
    salvage                 double precision,
    third_party_recovery    double precision,
    remark                  text,
    status                  varchar(3)              not null,
    created_at              timestamp default now() not null,
    created_by              integer                 not null,
    updated_at              timestamp,
    updated_by              text
);

alter table public.ins_claim_transaction_detail_temp
    owner to phillip;

create table if not exists public.ins_config_surcharge
(
    id               serial
        constraint ins_config_surcharge_pk
            primary key,
    claim_ratio_from double precision not null,
    claim_ratio_to   double precision,
    surcharge        double precision not null,
    remark           text,
    status           varchar(3) default 'ACT'::character varying,
    created_at       timestamp  default now(),
    created_by       varchar(10),
    updated_at       timestamp,
    updated_by       varchar(10),
    constraint ins_config_surcharge_unq
        unique (claim_ratio_from, claim_ratio_to)
);

alter table public.ins_config_surcharge
    owner to phillip;

create table if not exists public.ins_cust_classification
(
    group_code          text,
    cust_classification text,
    description         varchar(250),
    status              varchar(3),
    created_at          timestamp default now(),
    created_by          text,
    updated_at          timestamp,
    updated_by          text,
    description_kh      text,
    description_zh      varchar
);

alter table public.ins_cust_classification
    owner to phillip;

create table if not exists public.ins_cust_point_of_contact
(
    id          serial
        constraint "INS_CUST_POINT_OF_CONTACT_PK"
            primary key,
    customer_no varchar(20),
    branch_code varchar(3),
    name        varchar(50),
    phone1      varchar(20),
    phone2      varchar(20),
    email1      varchar(50),
    email2      varchar(50),
    remark      varchar(100),
    status      varchar(5),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10)
);

alter table public.ins_cust_point_of_contact
    owner to phillip;

create table if not exists public.ins_hs_age_setup
(
    id         serial
        constraint ins_hs_age_setup_pk
            primary key,
    status     char(3)   default 'ACT'::character varying,
    created_at timestamp default now(),
    created_by varchar,
    updated_at timestamp,
    updated_by varchar,
    from_year  integer,
    to_year    integer,
    rate       numeric not null
);

alter table public.ins_hs_age_setup
    owner to phillip;

create table if not exists public.ins_hs_plan_data_detail
(
    id                 serial
        constraint ins_hs_plan_data_detail_pk
            primary key,
    status             char(3)   default 'ACT'::character varying,
    created_at         timestamp default now(),
    created_by         varchar,
    updated_at         timestamp,
    updated_by         varchar,
    plan_id            integer not null,
    schema_detail_code varchar not null,
    name               varchar,
    sub                varchar,
    amount             integer,
    internal_text      varchar,
    display            varchar,
    clause_code        varchar,
    rate               numeric,
    discount           numeric,
    plan_1             numeric,
    plan_2             numeric,
    plan_3             numeric,
    plan_4             numeric,
    plan_5             numeric,
    name_kh            text,
    display_kh         text
);

alter table public.ins_hs_plan_data_detail
    owner to phillip;

create table if not exists public.ins_hs_schema_data
(
    id                 integer   default nextval('ins_hs_standard_data_id_seq'::regclass) not null
        constraint ins_hs_standard_data_pk
            primary key,
    status             char(3)   default 'ACT'::character varying,
    created_at         timestamp default now(),
    created_by         varchar,
    updated_at         timestamp,
    updated_by         varchar,
    master_data_id     integer                                                            not null,
    key                varchar                                                            not null,
    age_band           varchar,
    no_female          integer,
    no_person          integer,
    rate               numeric,
    plan_1             numeric,
    plan_2             numeric,
    plan_3             numeric,
    plan_4             numeric,
    plan_5             numeric,
    master_data_type   varchar                                                            not null,
    schema_type        varchar,
    schema_detail_code varchar,
    constraint data_unique_master_data_id_key_age_band_status_constraint
        unique (master_data_id, key, age_band, status),
    constraint data_optional_unique_constraint
        unique (master_data_id, master_data_type, key, schema_type, schema_detail_code, status)
);

alter table public.ins_hs_schema_data
    owner to phillip;

create table if not exists public.ins_hs_data_master
(
    id                     integer   default nextval('ins_hs_data_master_v1_id_seq'::regclass) not null
        constraint ins_hs_data_master_v1_pk
            primary key,
    status                 char(3)   default 'ACT'::character varying,
    created_at             timestamp default now(),
    created_by             varchar,
    updated_at             timestamp,
    updated_by             varchar,
    data_type              varchar,
    product_code           varchar,
    branch_code            varchar,
    customer_no            varchar,
    insurance_period_type  varchar,
    insurance_period_code  varchar,
    insurance_period_val   numeric,
    total_premium          numeric,
    negotiation_rate       numeric,
    remark                 varchar,
    joint_status           varchar(1),
    insured_name           varchar,
    insured_name_kh        varchar,
    insured_name_zh        varchar,
    business_code          varchar,
    sale_channel           varchar,
    commission_rate        numeric,
    handler_code           varchar,
    warranty               text,
    warranty_kh            text,
    memorandum             text,
    memorandum_kh          text,
    subjectivity           text,
    subjectivity_kh        text,
    policy_wording_version varchar,
    previous_id            integer,
    effective_date_from    date,
    effective_date_to      date,
    effective_month        double precision,
    effective_day          double precision,
    endorsement_e_date     date,
    endos_day_remaining    integer,
    endorsement_type       text,
    calc_option            varchar,
    surcharge              numeric,
    discount               numeric,
    insured_person_note    text,
    insured_person_note_kh text,
    remark_kh              text,
    refund_option          text,
    refund_percentage      double precision,
    premium_amt_bf_refund  double precision,
    custom_refund_amount   double precision
);

alter table public.ins_hs_data_master
    owner to phillip;

create table if not exists public.ins_hs_data_detail
(
    id                    integer   default nextval('ins_hs_data_master_detail_id_seq'::regclass) not null
        constraint ins_hs_data_detail_pk
            primary key,
    status                char(3)   default 'ACT'::character varying,
    created_at            timestamp default now(),
    created_by            varchar,
    updated_at            timestamp,
    updated_by            varchar,
    product_code          varchar,
    master_data_type      varchar,
    master_data_id        integer,
    name                  varchar                                                                 not null,
    occupation            varchar,
    gender                char,
    date_of_birth         date,
    standard_plan         varchar,
    optional_plan         varchar,
    endorsement_stage     text,
    endorsement_state     text,
    inception_date        date,
    endorsement_e_date    date,
    endos_day_remaining   integer,
    claim_request_count   integer,
    remark                varchar,
    previous_id           integer,
    refund_option         varchar,
    refund_percentage     double precision,
    premium_amt_bf_refund numeric,
    custom_refund_amount  numeric,
    other_benefit         text,
    premium               numeric,
    insured_person_uuid   uuid      default gen_random_uuid()                                     not null
);

alter table public.ins_hs_data_detail
    owner to phillip;

create table if not exists public.ins_hs_plan_data
(
    id             serial
        constraint ins_hs_plan_data_pk
            primary key,
    status         char(3)   default 'ACT'::character varying,
    created_at     timestamp default now(),
    created_by     varchar,
    updated_at     timestamp,
    updated_by     varchar,
    master_data_id integer,
    schema_type    varchar,
    schema_code    varchar not null,
    name           varchar not null,
    description    varchar
);

alter table public.ins_hs_plan_data
    owner to phillip;

create table if not exists public.ins_hs_schema
(
    id          serial
        constraint ins_hs_schema_pk
            primary key,
    status      char(3)   default 'ACT'::character varying,
    created_at  timestamp default now(),
    created_by  varchar,
    updated_at  timestamp,
    updated_by  varchar,
    schema_type varchar,
    code        varchar not null,
    name        varchar not null,
    description varchar
);

alter table public.ins_hs_schema
    owner to phillip;

create table if not exists public.ins_hs_schema_detail
(
    id            serial
        constraint ins_hs_schema_detail_pk
            primary key,
    status        char(3)   default 'ACT'::character varying,
    created_at    timestamp default now(),
    created_by    varchar,
    updated_at    timestamp,
    updated_by    varchar,
    schema_code   varchar not null,
    code          varchar not null,
    name          varchar,
    sub           varchar,
    amount        integer,
    internal_text varchar,
    display       varchar,
    clause_code   varchar,
    rate          numeric,
    discount      numeric,
    plan_1        numeric,
    plan_2        numeric,
    plan_3        numeric,
    plan_4        numeric,
    plan_5        numeric,
    name_kh       text,
    display_kh    text
);

alter table public.ins_hs_schema_detail
    owner to phillip;

create table if not exists public.ins_insurance_clause
(
    id                serial
        constraint ins_insurance_clause_pk
            primary key,
    code              varchar(4) not null,
    product_line_code varchar(25),
    clause_type       varchar(25),
    clause            varchar(500),
    clause_detail     text,
    version           integer,
    default_inclusion varchar(1),
    status            varchar(3),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10),
    sequence          integer,
    clause_kh         text,
    clause_detail_kh  text,
    clause_zh         text,
    clause_detail_zh  text
);

alter table public.ins_insurance_clause
    owner to phillip;

create table if not exists public.ins_insurance_ncd
(
    id           serial,
    product_code varchar(4)              not null,
    ncd          double precision        not null,
    description  text,
    status       varchar(5)              not null,
    created_at   timestamp default now() not null,
    created_by   text,
    updated_at   timestamp,
    updated_by   text
);

alter table public.ins_insurance_ncd
    owner to phillip;

create table if not exists public.ins_joint_account_detail
(
    id                serial
        constraint "INS_JNT_ACC_DETAIL_PK"
            primary key,
    customer_no       varchar(20),
    product_line_code varchar(25),
    product_code      varchar(5),
    data_id           integer,
    joint_level       varchar(25),
    permission        varchar(50),
    status            varchar(3),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10)
);

alter table public.ins_joint_account_detail
    owner to phillip;

create table if not exists public.ins_renewal_policy
(
    id                      serial
        constraint ins_renewal_policy_pk
            primary key,
    reference_id            integer not null,
    policy_no               varchar(25),
    version                 integer,
    cycle                   integer,
    document_no             varchar(25),
    branch_code             varchar(3),
    customer_no             varchar(20),
    product_line_code       varchar(25),
    product_code            varchar(5),
    ref_data_id             integer not null,
    data_id                 integer,
    sum_insured             double precision,
    premium                 double precision,
    policy_alt_no           varchar(50),
    account_code            varchar(6),
    handler_code            varchar(6),
    business_type           text,
    policy_type             text,
    claim_request_count     integer    default 0,
    status                  varchar(3) default 'PND'::character varying,
    submit_status           varchar(3) default 'DRF'::character varying,
    created_at              timestamp  default now(),
    created_by              varchar(10),
    updated_at              timestamp,
    updated_by              varchar(10),
    submitted_at            timestamp,
    submitted_by            text,
    submitted_remark        text,
    approved_at             timestamp,
    approved_by             text,
    approved_reason         text,
    accepted_at             timestamp,
    accepted_by             text,
    accepted_reason         text,
    total_claim_incurred    double precision,
    total_claim_paid        double precision,
    total_claim_outstanding double precision,
    total_gross_premium     double precision,
    claim_ratio             double precision,
    underwriting_year       integer not null,
    accept_status           varchar(3) default 'PND'::character varying,
    renewed_at              timestamp,
    renewed_by              text,
    renewed_remark          text,
    claim_incurred          double precision,
    claim_paid              double precision,
    claim_outstanding       double precision,
    constraint ins_renewal_policy_unq
        unique (policy_no, version, cycle)
);

alter table public.ins_renewal_policy
    owner to phillip;

create table if not exists public.ins_lov_vehicle_classification
(
    id          serial
        constraint "INS_LOV_VEHICLE_CLSFICATION_PK"
            primary key,
    code        text,
    description text,
    surcharge   double precision,
    discount    double precision,
    ncd         double precision,
    status      varchar(3),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10)
);

alter table public.ins_lov_vehicle_classification
    owner to phillip;

create table if not exists public.ins_lov_vehicle_rules
(
    id            serial
        constraint ins_lov_vehicle_rules_pk
            primary key,
    user_id       integer not null,
    make_id       integer,
    model_id      integer,
    allow_offline varchar(1),
    allow_online  varchar(1),
    status        varchar(3),
    created_at    timestamp default now(),
    created_by    varchar(10),
    updated_at    timestamp,
    updated_by    varchar(10)
);

alter table public.ins_lov_vehicle_rules
    owner to phillip;

create table if not exists public.ins_lov_vehicle_usage
(
    id           serial
        constraint "INS_LOV_VEHICLE_USAGE_PK"
            primary key,
    product_code text,
    name         text,
    description  text,
    status       varchar(3),
    created_at   timestamp default now(),
    created_by   text,
    updated_at   timestamp,
    updated_by   text
);

alter table public.ins_lov_vehicle_usage
    owner to phillip;

create table if not exists public.ins_mode_of_payment
(
    id           serial
        constraint "INS_MODE_OF_PAYMENT_PK"
            primary key,
    code         varchar(10) not null,
    type         varchar(25),
    name         varchar(60) not null,
    account_no   varchar(25) not null,
    account_name varchar(60) not null,
    ccy          text        not null,
    "default"    boolean     not null,
    status       varchar(3)  not null,
    created_at   timestamp   not null,
    created_by   text        not null,
    updated_at   timestamp,
    updated_by   text
);

alter table public.ins_mode_of_payment
    owner to phillip;

create table if not exists public.ins_partner_insurance_company
(
    id             serial
        constraint "INS_PRN_INS_COM_PK"
            primary key,
    type           varchar(25),
    name           varchar(500),
    address        text,
    contact_person varchar(500),
    position       varchar(500),
    phone          varchar(500),
    email          varchar(500),
    status         varchar(5),
    created_at     timestamp default now(),
    created_by     varchar(10),
    updated_at     timestamp,
    updated_by     varchar(10)
);

alter table public.ins_partner_insurance_company
    owner to phillip;

create table if not exists public.ins_prod_component
(
    id                      serial
        constraint ins_prod_component_pk
            primary key,
    product_code            varchar(5),
    code                    varchar(25),
    name                    varchar(50),
    description             varchar(50),
    detail                  text,
    mandatory               boolean,
    type                    varchar(1),
    data_type               varchar(25),
    value                   double precision,
    status                  varchar(3),
    created_at              timestamp default now(),
    created_by              varchar(10),
    updated_at              timestamp,
    updated_by              varchar(10),
    seq                     integer,
    name_kh                 text,
    description_kh          text,
    detail_kh               text,
    name_zh                 text,
    description_zh          text,
    detail_zh               text,
    deductible_label        text,
    deductible_label_kh     text,
    deductible_label_zh     text,
    is_required_vehicle_val boolean
);

alter table public.ins_prod_component
    owner to phillip;

create table if not exists public.ins_prod_deductible_detail
(
    id                serial
        constraint ins_prod_deductible_detail_pk
            primary key,
    product_code      varchar(5),
    ms_tbl_handler    varchar(50),
    data_type         varchar(25),
    data_id           integer,
    dt_tbl_handler    varchar(50),
    detail_id         integer,
    comp_code         varchar(25),
    value             varchar(250),
    status            varchar(3),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10),
    seq               integer,
    cond_value_type   varchar,
    cond_value        double precision,
    min_value         double precision,
    max_value         double precision,
    currency          varchar(3),
    vehicle_value     double precision,
    vehicle_usage     varchar,
    endorsement_stage varchar,
    endorsement_state varchar,
    value_label       varchar
);

alter table public.ins_prod_deductible_detail
    owner to phillip;

create table if not exists public.ins_policy_customer
(
    id          serial
        constraint "INS_POLICY_CUSTOMER_PK"
            primary key,
    policy_id   integer,
    policy_no   varchar(25),
    branch_code varchar(3),
    customer_no varchar(20),
    join_type   varchar(20),
    status      varchar(5),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10)
);

alter table public.ins_policy_customer
    owner to phillip;

create table if not exists public.ins_policy_endor_commission_hist
(
    id                    serial
        constraint "INS_POLICY_ENDOR_COMMISSION_HIST_PK"
            primary key,
    policy_id             integer,
    endorsement_stage     text,
    endorsement_state     text,
    data_id               integer,
    detail_id             integer,
    business_category     text,
    business_code         text,
    gross_written_premium double precision,
    premium_tax_fee_rate  double precision,
    premium_tax_fee       double precision,
    net_written_premium   double precision,
    commission_rate       double precision,
    commission_amount     double precision,
    witholding_tax_rate   double precision,
    witholding_tax        double precision,
    commission_due_amount double precision,
    status                varchar(3),
    created_at            timestamp default now(),
    created_by            varchar(10),
    updated_at            timestamp,
    updated_by            varchar(10)
);

alter table public.ins_policy_endor_commission_hist
    owner to phillip;

create table if not exists public.ins_policy_trn_log
(
    id              serial
        constraint "INS_POLICT_TRN_LOG_PK"
            primary key,
    policy_id       integer,
    policy_no       text,
    event_code      text,
    ccy             varchar(3),
    fcy_amount      double precision,
    exch_rate       double precision,
    lcy_amount      double precision,
    trn_date        date,
    external_ref_no text,
    remark          text,
    trn_ref_no      text,
    status          varchar(3),
    created_at      timestamp default now(),
    created_by      varchar(10),
    updated_at      timestamp,
    updated_by      varchar(10)
);

alter table public.ins_policy_trn_log
    owner to phillip;

create table if not exists public.ins_policy_wording_version
(
    id                serial
        constraint "INS_POLICY_WORDING_VERSION_PK"
            primary key,
    product_line_code text,
    product_code      text,
    policy_wording    text,
    year              integer,
    status            varchar(3),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10),
    is_default        varchar(1)
);

alter table public.ins_policy_wording_version
    owner to phillip;

create table if not exists public.ins_prod_auto_vehicle_cubic
(
    id          serial
        constraint ins_prod_auto_vehicle_cubic_pk
            primary key,
    code        varchar(255),
    description varchar(255),
    status      varchar(3),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10)
);

alter table public.ins_prod_auto_vehicle_cubic
    owner to phillip;

create table if not exists public.ins_prod_auto_vehicle_maker
(
    id           serial
        constraint ins_prod_auto_vehicle_maker_pk
            primary key,
    brand        varchar(255),
    description  varchar(255),
    product_code varchar(255),
    status       varchar(3),
    created_at   timestamp default now(),
    created_by   varchar(10),
    updated_at   timestamp,
    updated_by   varchar(10)
);

alter table public.ins_prod_auto_vehicle_maker
    owner to phillip;

create table if not exists public.ins_prod_auto_vehicle_model
(
    id           serial
        constraint ins_prod_auto_vehicle_model_pk
            primary key,
    maker_id     integer,
    model        varchar(50),
    description  varchar(255),
    type         varchar(50),
    product_code varchar(255),
    status       varchar(3),
    created_at   timestamp default now(),
    created_by   varchar(10),
    updated_at   timestamp,
    updated_by   varchar(10)
);

alter table public.ins_prod_auto_vehicle_model
    owner to phillip;

create table if not exists public.ins_prod_comp_data
(
    id             serial
        constraint ins_prod_comp_data_pk
            primary key,
    data_type      varchar(25),
    data_id        integer,
    product_code   varchar(5),
    comp_data_type varchar(1),
    comp_detail_id integer,
    key            varchar(25),
    value          double precision,
    status         varchar(3),
    created_at     timestamp default now(),
    created_by     varchar(10),
    updated_at     timestamp,
    updated_by     varchar(10),
    constraint data_unique
        unique (data_type, data_id, product_code, comp_data_type, comp_detail_id, key, status)
);

alter table public.ins_prod_comp_data
    owner to phillip;

create table if not exists public.ins_prod_comp_formula
(
    id             serial
        constraint ins_prod_comp_formula_pk
            primary key,
    product_code   varchar(5),
    component_code varchar(25),
    calc_option    varchar(25),
    formula_code   varchar(25),
    frm_calc_seq   integer,
    status         varchar(3),
    created_at     timestamp default now(),
    created_by     varchar(10),
    updated_at     timestamp,
    updated_by     varchar(10)
);

alter table public.ins_prod_comp_formula
    owner to phillip;

create table if not exists public.ins_prod_comp_frm_elem
(
    id             serial
        constraint ins_prod_comp_frm_elem_pk
            primary key,
    product_code   varchar(5),
    component_code varchar(25),
    calc_option    varchar(25),
    formula_code   varchar(25),
    elem_code      varchar(25),
    elem_type      varchar(2),
    elem_datatype  varchar(25),
    status         varchar(3),
    created_at     timestamp default now(),
    created_by     varchar(10),
    updated_at     timestamp,
    updated_by     varchar(10)
);

alter table public.ins_prod_comp_frm_elem
    owner to phillip;

create table if not exists public.ins_prod_comp_frm_expr
(
    id             serial
        constraint ins_prod_comp_frm_expr_pk
            primary key,
    product_code   varchar(5),
    component_code varchar(25),
    calc_option    varchar(25),
    formula_code   varchar(25),
    expr_line      integer,
    expr_type      varchar(25),
    cond_type      varchar(1),
    cond_expr      text,
    formula_expr   text,
    status         varchar(3),
    created_at     timestamp default now(),
    created_by     varchar(10),
    updated_at     timestamp,
    updated_by     varchar(10)
);

alter table public.ins_prod_comp_frm_expr
    owner to phillip;

create table if not exists public.ins_prod_cond_rating
(
    id           serial
        constraint ins_prod_cond_rating_pk
            primary key,
    product_code varchar(5),
    code         varchar(50),
    description  varchar(1000),
    key          varchar(50),
    value        varchar(250),
    status       varchar(3),
    created_at   timestamp default now(),
    created_by   varchar(10),
    updated_at   timestamp,
    updated_by   varchar(10),
    cond_expr    text,
    cond_type    varchar(1)
);

alter table public.ins_prod_cond_rating
    owner to phillip;

create table if not exists public.ins_prod_cover_package
(
    id           serial
        constraint ins_prod_cover_package_pk
            primary key,
    product_code varchar(5),
    name         varchar(50),
    description  varchar(1000),
    status       varchar(3),
    created_at   timestamp default now(),
    created_by   varchar(10),
    updated_at   timestamp,
    updated_by   varchar(10)
);

alter table public.ins_prod_cover_package
    owner to phillip;

create table if not exists public.ins_prod_cpkg_comp
(
    id           serial
        constraint ins_prod_cpkg_comp_pk
            primary key,
    product_code varchar(5),
    cpkg_id      integer,
    comp_code    varchar(25),
    status       varchar(3),
    created_at   timestamp default now(),
    created_by   varchar(10),
    updated_at   timestamp,
    updated_by   varchar(10)
);

alter table public.ins_prod_cpkg_comp
    owner to phillip;

create table if not exists public.ins_prod_deductible
(
    id              serial
        constraint ins_prod_deductible_pk
            primary key,
    product_code    varchar(5),
    comp_code       varchar(25),
    label           varchar(100),
    label_kh        text,
    label_zh        text,
    description     varchar(1000),
    description_kh  text,
    description_zh  text,
    cond_type       varchar(1),
    cond_level      varchar(5),
    cond_expr       text,
    value           varchar(250),
    cond_value_type varchar,
    cond_value      double precision,
    min_value       double precision,
    max_value       double precision,
    currency        varchar(3),
    value_kh        text,
    value_zh        text,
    status          varchar(3),
    created_at      timestamp default now(),
    created_by      varchar(10),
    updated_at      timestamp,
    updated_by      varchar(10)
);

alter table public.ins_prod_deductible
    owner to phillip;

create table if not exists public.ins_product_line
(
    id          serial
        constraint "INS_PRODUCT_LINE_PK"
            primary key,
    code        varchar(25),
    description varchar(50),
    alt_code    varchar(50),
    status      varchar(3),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10)
);

alter table public.ins_product_line
    owner to phillip;

create table if not exists public.ins_reinsurance
(
    id          serial
        constraint "INS_REINSURANCE_PK"
            primary key,
    code        text,
    name        text,
    description text,
    status      varchar(3),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10)
);

alter table public.ins_reinsurance
    owner to phillip;

create table if not exists public.ins_reinsurance_config
(
    id                serial
        constraint "INS_REINSURANCE_CONFIG_PK"
            primary key,
    product_line_code varchar(25),
    product_code      varchar(5),
    reinsurance_type  text,
    reinsurance_code  text,
    partner_code      text,
    start_from        date,
    start_to          date,
    leaf              varchar(1),
    lvl               integer,
    parent_code       varchar(50),
    share_basis       varchar(50),
    uw_year           integer,
    share             double precision,
    amount_cap        double precision,
    ri_commission     double precision,
    tax_fee           double precision,
    status            varchar(5),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10)
);

alter table public.ins_reinsurance_config
    owner to phillip;

create table if not exists public.ins_reinsurance_partner_group
(
    id          serial
        constraint "INS_REINSURANCE_PARTNER_GROUP_PK"
            primary key,
    code        text,
    name        text,
    description text,
    status      varchar(3),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10)
);

alter table public.ins_reinsurance_partner_group
    owner to phillip;

create table if not exists public.ins_reinsurance_type
(
    id          serial
        constraint "INS_REINSURANCE_TYPE_PK"
            primary key,
    code        text,
    name        text,
    description text,
    status      varchar(3),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10)
);

alter table public.ins_reinsurance_type
    owner to phillip;

create table if not exists public.ins_response_code
(
    code        varchar(7) not null
        constraint "INS_RESPONSE_CODE_PK"
            primary key,
    category    varchar(20),
    type        varchar(3),
    http_status varchar(20),
    description varchar(500),
    message_en  varchar(50),
    message_kh  varchar(50),
    message_cn  varchar(50),
    status      varchar(5),
    created_at  timestamp default now(),
    created_by  varchar(10),
    updated_at  timestamp,
    updated_by  varchar(10)
);

alter table public.ins_response_code
    owner to phillip;

create table if not exists public.ins_tmp_script
(
    error text
);

alter table public.ins_tmp_script
    owner to phillip;

create table if not exists public.ins_treaty_config
(
    id                serial
        constraint "INS_TREATY_CONFIG_PK"
            primary key,
    product_line_code varchar(25),
    product_code      varchar(5),
    treaty_type       varchar(50),
    treaty_code       varchar(50),
    start_from        date,
    start_to          date,
    leaf              varchar(1),
    lvl               integer,
    parent_code       varchar(50),
    share_basis       varchar(50),
    uw_year           integer,
    share             double precision,
    amount_cap        double precision,
    ri_commission     double precision,
    tax_fee           double precision,
    status            varchar(5),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10)
);

alter table public.ins_treaty_config
    owner to phillip;

create table if not exists public.ins_vehicle
(
    id           serial,
    name         varchar(255) not null,
    product_code varchar(255) not null,
    code         varchar(255),
    description  varchar(255),
    status       varchar(5)   not null,
    created_at   timestamp(0),
    created_by   varchar(255),
    updated_at   timestamp(0),
    updated_by   varchar(255)
);

alter table public.ins_vehicle
    owner to phillip;

create table if not exists public.ins_vehicle_model
(
    id           serial,
    model_number varchar(255) not null,
    vehicle_id   integer      not null,
    description  varchar(255),
    status       varchar(5)   not null,
    created_at   timestamp(0),
    created_by   varchar(255),
    updated_at   timestamp(0),
    updated_by   varchar(255)
);

alter table public.ins_vehicle_model
    owner to phillip;

create table if not exists public.insured_info
(
    age              integer,
    age_count        integer,
    female_age_count integer
);

alter table public.insured_info
    owner to phillip;

create table if not exists public.mb_customer
(
    id                serial
        constraint "MB_CUSTOMER_PK"
            primary key,
    name              varchar(50),
    id_no             varchar(20),
    cif_no            varchar(20),
    phone_no          varchar(15),
    email             varchar(20),
    status            varchar(3),
    gender            varchar(1),
    activate_at       timestamp(6),
    confirm_at        timestamp(6),
    confirm_by        varchar(20),
    register_channel  varchar(20),
    username          varchar(20),
    password          varchar(250),
    pin               varchar(250),
    package_code      varchar(20),
    change_pwd_at     timestamp(6),
    change_pin_at     timestamp(6),
    last_login_at     timestamp(6),
    type              varchar(20),
    key               varchar(600),
    force_change_pwd  varchar(1),
    invalid_pin_count integer,
    invalid_pin_at    timestamp(6),
    full_name         varchar(50),
    avatar            varchar(100),
    ref_customer_id   varchar(100),
    bakong_id         varchar(250),
    created_at        timestamp(6) default now(),
    created_by        varchar(20),
    updated_at        timestamp(6),
    updated_by        varchar(20)
);

alter table public.mb_customer
    owner to phillip;

create table if not exists public.mi_customer_config
(
    id          integer      default nextval('mb_customer_config_id_seq'::regclass) not null
        constraint "MB_CUSTOMER_CONFIG_PK"
            primary key,
    customer_id integer,
    key         varchar(250),
    value       varchar(250),
    description varchar(250),
    status      varchar(3),
    created_at  timestamp(6) default now(),
    created_by  varchar(20),
    updated_at  timestamp(6),
    updated_by  varchar(20)
);

alter table public.mi_customer_config
    owner to phillip;

create table if not exists public.mi_customer_history
(
    id          integer      default nextval('mb_customer_history_id_seq'::regclass) not null
        constraint "MB_CUSTOMER_HISTORY_PK"
            primary key,
    customer_id integer,
    username    varchar(20),
    type        varchar(10),
    action      varchar(10),
    created_at  timestamp(6) default now(),
    created_by  varchar(50),
    bakong_id   varchar(125)
);

alter table public.mi_customer_history
    owner to phillip;

create table if not exists public.mi_customer_session
(
    id            integer      default nextval('mb_customer_session_id_seq'::regclass) not null
        constraint "MB_CUSTOMER_SESSION_PK"
            primary key,
    customer_no   varchar,
    customer_name varchar(100),
    ip            varchar(100),
    log_at        timestamp(6),
    status        varchar(100),
    device_id     integer,
    login_session varchar(250),
    app_version   varchar(100),
    created_at    timestamp(6) default now(),
    updated_at    timestamp(6)
);

alter table public.mi_customer_session
    owner to phillip;

create table if not exists public.mi_device
(
    id            integer      default nextval('mb_device_id_seq'::regclass) not null
        constraint "MB_DEVICE_PK"
            primary key,
    model         varchar(100),
    os_type       varchar(120),
    os_version    varchar(20),
    imie          varchar(150),
    device_status varchar(1),
    customer_no   varchar,
    customer_name varchar(50),
    ip            varchar(100),
    status        varchar(3),
    created_at    timestamp(6) default now(),
    updated_at    timestamp(6)
);

alter table public.mi_device
    owner to phillip;

create table if not exists public.mi_error_code
(
    id          integer      default nextval('mb_error_code_id_seq'::regclass) not null
        constraint "MB_ERROR_CODE_PK"
            primary key,
    code        varchar(50),
    http_status varchar(10),
    description varchar(1000),
    message_en  varchar(1000),
    message_kh  varchar(1000),
    key         varchar(10),
    type        varchar(10),
    message     varchar(1000),
    created_at  timestamp(6) default now()
);

alter table public.mi_error_code
    owner to phillip;

create table if not exists public.mi_module
(
    id          integer      default nextval('mb_module_id_seq'::regclass) not null
        constraint "MB_MODULE_PK"
            primary key,
    code        varchar(10)                                                not null,
    name_en     varchar(100),
    name_kh     varchar(100),
    name_cn     varchar(100),
    image_url   varchar(250),
    package_id  integer,
    status      varchar(3),
    seq_no      integer,
    created_at  timestamp(6) default now(),
    created_by  varchar(20),
    updated_at  timestamp(6),
    updated_by  varchar(20),
    is_deeplink varchar(1)
);

alter table public.mi_module
    owner to phillip;

create table if not exists public.mi_request_log
(
    id            integer      default nextval('mb_request_log_id_seq'::regclass) not null
        constraint "MB_REQUEST_LOG_PK"
            primary key,
    url           varchar(100),
    created_at    timestamp(6) default now(),
    ip_address    varchar(100),
    user_agent    varchar(250),
    user_session  varchar(100),
    device_id     integer,
    http_status   varchar(10),
    response_code varchar(10),
    os            varchar(10)
);

alter table public.mi_request_log
    owner to phillip;

create table if not exists public.mi_response_code
(
    id          integer default nextval('mi_reponse_code_id_seq'::regclass) not null,
    code        varchar(50),
    http_status varchar(20),
    key         varchar(20),
    type        varchar(15),
    description varchar(20),
    status      varchar(3),
    message_en  varchar(1),
    message_kh  timestamp,
    created_by  varchar(20),
    updated_at  timestamp,
    updated_by  varchar(20),
    created_at  information_schema.time_stamp
);

alter table public.mi_response_code
    owner to phillip;

create table if not exists public.migrations
(
    id        serial
        primary key,
    migration varchar(255) not null,
    batch     integer      not null
);

alter table public.migrations
    owner to phillip;

create table if not exists public.oauth_access_tokens
(
    id         varchar(100) not null
        primary key,
    user_id    bigint,
    client_id  bigint       not null,
    name       varchar(255),
    scopes     text,
    revoked    boolean      not null,
    created_at timestamp(0),
    updated_at timestamp(0),
    expires_at timestamp(0)
);

alter table public.oauth_access_tokens
    owner to phillip;

create index if not exists oauth_access_tokens_user_id_index
    on public.oauth_access_tokens (user_id);

create table if not exists public.oauth_auth_codes
(
    id         varchar(100) not null
        primary key,
    user_id    bigint       not null,
    client_id  bigint       not null,
    scopes     text,
    revoked    boolean      not null,
    expires_at timestamp(0)
);

alter table public.oauth_auth_codes
    owner to phillip;

create index if not exists oauth_auth_codes_user_id_index
    on public.oauth_auth_codes (user_id);

create table if not exists public.oauth_clients
(
    id                     bigserial
        primary key,
    user_id                bigint,
    name                   varchar(255) not null,
    secret                 varchar(100),
    provider               varchar(255),
    redirect               text         not null,
    personal_access_client boolean      not null,
    password_client        boolean      not null,
    revoked                boolean      not null,
    created_at             timestamp(0),
    updated_at             timestamp(0)
);

alter table public.oauth_clients
    owner to phillip;

create index if not exists oauth_clients_user_id_index
    on public.oauth_clients (user_id);

create table if not exists public.oauth_personal_access_clients
(
    id         bigserial
        primary key,
    client_id  bigint not null,
    created_at timestamp(0),
    updated_at timestamp(0)
);

alter table public.oauth_personal_access_clients
    owner to phillip;

create table if not exists public.oauth_refresh_tokens
(
    id              varchar(100) not null
        primary key,
    access_token_id varchar(100) not null,
    revoked         boolean      not null,
    expires_at      timestamp(0)
);

alter table public.oauth_refresh_tokens
    owner to phillip;

create index if not exists oauth_refresh_tokens_access_token_id_index
    on public.oauth_refresh_tokens (access_token_id);

create table if not exists public.password_resets
(
    email      varchar(255) not null,
    token      varchar(255) not null,
    created_at timestamp(0)
);

alter table public.password_resets
    owner to phillip;

create index if not exists password_resets_email_index
    on public.password_resets (email);

create table if not exists public.personal_access_tokens
(
    id             bigserial
        primary key,
    tokenable_type varchar(255) not null,
    tokenable_id   bigint       not null,
    name           varchar(255) not null,
    token          varchar(64)  not null
        constraint personal_access_tokens_token_unique
            unique,
    abilities      text,
    last_used_at   timestamp(0),
    created_at     timestamp(0),
    updated_at     timestamp(0)
);

alter table public.personal_access_tokens
    owner to phillip;

create index if not exists personal_access_tokens_tokenable_type_tokenable_id_index
    on public.personal_access_tokens (tokenable_type, tokenable_id);

create table if not exists public.q_premium
(
    plan_1 numeric,
    plan_2 numeric,
    plan_3 numeric,
    plan_4 numeric,
    plan_5 numeric
);

alter table public.q_premium
    owner to phillip;

create table if not exists public.quotation_no
(
    lpad text
);

alter table public.quotation_no
    owner to phillip;

create table if not exists public.r_dm_data
(
    id                    integer,
    product_code          varchar(5),
    product_name          varchar(50),
    data_type             varchar(25),
    q_p_code              varchar,
    q_p_version           integer,
    policy_cycle          integer,
    q_p_alt_no            varchar,
    q_p_status            varchar,
    insurance_period_type varchar(50),
    insurance_period_code varchar(50),
    insurance_period_val  double precision,
    effective_date_from   date,
    effective_date_to     date,
    effective_day         double precision,
    effective_month       double precision,
    endorsement_type      text,
    endorsement_e_date    date,
    endos_day_remaining   integer,
    sum_insured           double precision,
    calc_option           varchar(50),
    total_premium         double precision,
    remark                varchar(500)
);

alter table public.r_dm_data
    owner to phillip;

create table if not exists public.surcharge_code
(
    code varchar
);

alter table public.surcharge_code
    owner to phillip;

create table if not exists public.tmp_data
(
    id         serial,
    data_type  varchar(25),
    data_msg   text,
    status     varchar(3),
    created_at timestamp default now(),
    created_by varchar(10),
    updated_at timestamp,
    updated_by varchar(10)
);

alter table public.tmp_data
    owner to phillip;

create table if not exists public.tmp_ins_config
(
    id         serial
        constraint "TMP_INS_CONFIG_PK"
            primary key,
    code       varchar(100),
    value      varchar(100),
    status     varchar(3),
    created_at timestamp(6) default now(),
    created_by varchar(20),
    updated_at timestamp(6),
    updated_by varchar(20)
);

alter table public.tmp_ins_config
    owner to phillip;

create table if not exists public.tmp_ins_customer
(
    id          serial
        constraint "TMP_INS_CUSTOMER_PK"
            primary key,
    customer_no varchar(9),
    branch_code varchar(3),
    first_name  varchar(100),
    last_name   varchar(100),
    phone_no    varchar(25),
    dob         date,
    address     text,
    status      varchar(3),
    created_at  timestamp(6) default now(),
    created_by  varchar(20),
    updated_at  timestamp(6),
    updated_by  varchar(20)
);

alter table public.tmp_ins_customer
    owner to phillip;

create table if not exists public.tmp_ins_customer_attr
(
    id          serial
        constraint "TMP_INS_CUSTOMER_ATTR_PK"
            primary key,
    customer_no varchar(9),
    label       varchar(100),
    value       varchar(500),
    status      varchar(3),
    created_at  timestamp(6) default now(),
    created_by  varchar(20),
    updated_at  timestamp(6),
    updated_by  varchar(20)
);

alter table public.tmp_ins_customer_attr
    owner to phillip;

create table if not exists public.tmp_ins_policy
(
    id          serial
        constraint "TMP_INS_POLICY_PK"
            primary key,
    customer_no varchar(9),
    policy_no   varchar(9),
    branch_code varchar(3),
    sum_insured double precision,
    premium     double precision,
    expired_on  date,
    status      varchar(3),
    created_at  timestamp(6) default now(),
    created_by  varchar(20),
    updated_at  timestamp(6),
    updated_by  varchar(20)
);

alter table public.tmp_ins_policy
    owner to phillip;

create table if not exists public.tmp_ins_policy_record_log
(
    id          serial
        constraint "TMP_INS_POLICY_RECORD_LOG_PK"
            primary key,
    ref_no      varchar(16),
    event_sr_no integer,
    event       varchar(16),
    event_desc  varchar(100),
    entry_sr_no integer,
    branch_code varchar(3),
    policy_no   varchar(9),
    ccy         varchar(3),
    drcr_ind    varchar(1),
    amount      double precision,
    value_date  date,
    note        text,
    status      varchar(3),
    created_at  timestamp(6) default now(),
    created_by  varchar(20),
    updated_at  timestamp(6),
    updated_by  varchar(20),
    customer_no varchar(15)
);

alter table public.tmp_ins_policy_record_log
    owner to phillip;

create table if not exists public.tmp_vehicle_list
(
    product_code    text,
    make            text,
    model           text,
    body_style      text,
    classification  text,
    risk_acceptance text,
    com_model       text
);

alter table public.tmp_vehicle_list
    owner to phillip;

create table if not exists public.totals
(
    sum        bigint,
    no_persons bigint,
    plan_1     numeric,
    plan_2     numeric,
    plan_3     numeric,
    plan_4     numeric,
    plan_5     numeric
);

alter table public.totals
    owner to phillip;

create table if not exists public.users
(
    id                bigserial
        primary key,
    name              varchar(255) not null,
    email             varchar(255) not null
        constraint users_email_unique
            unique,
    email_verified_at timestamp(0),
    password          varchar(255) not null,
    remember_token    varchar(100),
    created_at        timestamp(0),
    updated_at        timestamp(0)
);

alter table public.users
    owner to phillip;

create table if not exists public.ins_hs_quotation
(
    id                serial
        constraint "INS_HS_QUOTATION_PK"
            primary key,
    quotation_no      varchar(25),
    version           integer,
    branch_code       varchar(3),
    customer_no       varchar(20),
    product_line_code varchar(25),
    product_code      varchar(5),
    data_id           integer,
    sum_insured       double precision,
    premium           double precision,
    quotation_alt_no  varchar(50),
    status            varchar(5),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10),
    account_code      varchar(6),
    handler_code      varchar(6),
    document_no       varchar(50),
    approved_at       timestamp,
    approved_by       varchar(10),
    approved_status   varchar(5),
    accepted_status   varchar(5),
    accepted_at       timestamp,
    accepted_by       varchar(10),
    accepted_reason   text,
    approved_reason   text
);

alter table public.ins_hs_quotation
    owner to phillip;

create table if not exists public.ins_hs_data_clause
(
    id         serial
        constraint ins_hs_data_clause_pk
            primary key,
    data_id    integer,
    clause_id  integer,
    status     varchar(3),
    created_at timestamp default now(),
    created_by varchar(10),
    updated_at timestamp,
    updated_by varchar(10)
);

alter table public.ins_hs_data_clause
    owner to phillip;

create table if not exists public.ins_hs_policy
(
    id                      serial
        constraint "INS_HS_POLICY_PK"
            primary key,
    policy_no               varchar(25),
    version                 integer,
    cycle                   integer   default 0 not null,
    document_no             varchar(25),
    quotation_id            integer,
    branch_code             varchar(3),
    customer_no             varchar(20),
    product_line_code       varchar(25),
    product_code            varchar(5),
    data_id                 integer,
    sum_insured             double precision,
    premium                 double precision,
    policy_alt_no           varchar(50),
    account_code            varchar(6),
    handler_code            varchar(6),
    status                  varchar(5),
    created_at              timestamp default now(),
    created_by              varchar(10),
    updated_at              timestamp,
    updated_by              varchar(10),
    approved_at             timestamp,
    approved_by             varchar(10),
    business_type           text,
    policy_type             text,
    approved_status         varchar(3),
    approved_reason         text,
    endorsement_description text,
    request_amount          double precision,
    renewal_reference_id    integer,
    reference_source        varchar,
    reference_id            integer,
    constraint ins_hs_policy_ref_source_ref_id_unq
        unique (reference_source, reference_id)
);

alter table public.ins_hs_policy
    owner to phillip;

create table if not exists public.ins_hs_policy_invoice_note
(
    id                 serial
        constraint "INS_HS_POLICY_INVOICE_NOTE_PK"
            primary key,
    policy_id          integer          not null,
    policy_document_no varchar(20)      not null,
    type               varchar(12)      not null,
    inv_cdn_no         varchar(15)      not null,
    seq_no             varchar(10)      not null,
    issue_date         date             not null,
    code               varchar(10)      not null,
    customer_name      text,
    address            text,
    tin_code           varchar(20),
    product_name       varchar(50),
    insurance_period   text,
    endorsement_e_date date,
    total_premuim      double precision not null,
    exch_rate          double precision not null,
    status             varchar(3)       not null,
    created_at         timestamp        not null,
    created_by         text,
    updated_at         timestamp,
    updated_by         text
);

alter table public.ins_hs_policy_invoice_note
    owner to phillip;

create table if not exists public.ins_hs_policy_commission_data
(
    id                    serial
        constraint "INS_HS_POLICY_COMMISSION_DATA_PK"
            primary key,
    policy_id             integer,
    policy_no             text,
    business_category     text,
    business_code         text,
    gross_written_premium double precision,
    premium_tax_fee_rate  double precision,
    premium_tax_fee       double precision,
    net_written_premium   double precision,
    commission_rate       double precision,
    commission_amount     double precision,
    witholding_tax_rate   double precision,
    witholding_tax        double precision,
    commission_due_amount double precision,
    status                varchar(3),
    created_at            timestamp default now(),
    created_by            varchar(10),
    updated_at            timestamp,
    updated_by            varchar(10),
    data_id               integer
);

alter table public.ins_hs_policy_commission_data
    owner to phillip;

create table if not exists public.ins_hs_translation
(
    id          serial,
    key         text,
    lang_code   char(2),
    translation text,
    field_name  text,
    table_name  text,
    status      char(3)   default 'ACT'::character varying,
    created_at  timestamp default now(),
    created_by  text,
    updated_at  timestamp,
    updated_by  text,
    constraint ois_translation_unq
        unique (key, lang_code, table_name, field_name)
);

alter table public.ins_hs_translation
    owner to phillip;

create table if not exists public.sys_enum
(
    id          serial
        constraint sys_enum_pk
            primary key,
    group_code  varchar not null,
    code        varchar not null,
    name        text    not null,
    description text,
    seq_no      varchar,
    table_name  varchar,
    status      varchar(3) default 'ACT'::character varying,
    created_at  timestamp  default now(),
    created_by  text,
    updated_at  timestamp,
    updated_by  text,
    type_code   text,
    constraint sys_enum_unq
        unique (group_code, code)
);

alter table public.sys_enum
    owner to phillip;

create table if not exists public.ins_hs_reinsurance_config
(
    id                serial
        constraint "INS_HS_REINSURANCE_CONFIG_PK"
            primary key,
    product_line_code varchar(25),
    product_code      varchar(5),
    reinsurance_type  text,
    reinsurance_code  text,
    partner_code      text,
    start_from        date,
    start_to          date,
    leaf              varchar(1),
    lvl               integer,
    parent_code       varchar(50),
    share_basis       varchar(50),
    uw_year           integer,
    share             double precision,
    amount_cap        double precision,
    ri_commission     double precision,
    tax_fee           double precision,
    status            varchar(5),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10)
);

alter table public.ins_hs_reinsurance_config
    owner to phillip;

create table if not exists public.ins_hs_reinsurance_data
(
    id                serial
        constraint "INS_HS_REINSURANCE_DATA_PK"
            primary key,
    policy_id         integer,
    data_id           integer,
    product_line_code varchar(25),
    product_code      varchar(5),
    uw_year           integer,
    treaty_code       varchar(50),
    lvl               integer,
    parent_code       varchar(50),
    share             double precision,
    premium           double precision,
    ri_commission     double precision,
    ri_commission_amt double precision,
    tax_fee           double precision,
    tax_fee_amt       double precision,
    net_premium       double precision,
    endorsement_stage text,
    status            varchar(5),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10)
);

alter table public.ins_hs_reinsurance_data
    owner to phillip;

create table if not exists public.tmp_data2
(
    product_code          text,
    cover_pkg             text,
    vehicle_value         double precision,
    calc_option           text,
    insurance_period_type text,
    effective_month       integer,
    insurance_period_val  double precision,
    surcharge             double precision,
    discount              double precision,
    ncd                   double precision,
    negotiation_rate      double precision,
    passenger             double precision,
    tonnage               double precision
);

alter table public.tmp_data2
    owner to phillip;

create table if not exists public.ins_hs_schema_data_sequences
(
    id          serial
        constraint ins_hs_schema_data_sequences_pk
            primary key,
    status      char(3)   default 'ACT'::character varying not null,
    created_at  timestamp default now()                    not null,
    created_by  varchar                                    not null,
    updated_at  timestamp,
    updated_by  varchar,
    key         varchar                                    not null,
    schema_type varchar,
    seq         integer                                    not null
);

alter table public.ins_hs_schema_data_sequences
    owner to phillip;

create table if not exists public.ins_hs_clinic
(
    id             serial
        constraint ins_hs_clinic_pk
            primary key,
    status         char(3)   default 'ACT'::character varying not null,
    type           varchar                                    not null,
    name           varchar                                    not null
        constraint ins_hs_clinic_pk_2
            unique,
    address        text,
    contact_name   varchar,
    contact_number varchar,
    latitude       varchar,
    longitude      varchar,
    created_at     timestamp default now()                    not null,
    created_by     varchar,
    updated_at     timestamp,
    updated_by     text
);

alter table public.ins_hs_clinic
    owner to phillip;

create table if not exists public.ins_hs_claim
(
    id                       serial
        constraint ins_hs_claim_pk
            primary key,
    status                   char(3)   default 'ACT'::character varying not null,
    seq_no                   varchar                                    not null,
    claim_no                 text                                       not null
        constraint ins_hs_claim_pk_2
            unique,
    policy_id                integer                                    not null,
    data_id                  integer                                    not null,
    data_detail_id           integer                                    not null,
    remark                   text,
    notification_date        timestamp                                  not null,
    schema_plan              varchar                                    not null,
    schema_type              varchar                                    not null,
    schema_detail_code       varchar,
    cause_of_loss            varchar,
    cause_of_loss_disability varchar,
    date_of_loss             timestamp                                  not null,
    location_of_loss         varchar                                    not null,
    loss_description         text,
    reserve_amount           numeric,
    processing_month         date      default now()                    not null,
    insured_period_from      date                                       not null,
    insured_period_to        date                                       not null,
    clinic_id                integer                                    not null,
    created_at               timestamp default now()                    not null,
    created_by               integer                                    not null,
    updated_at               timestamp,
    updated_by               integer,
    approved_by              integer,
    approved_cmt             text,
    approved_status          varchar(5),
    approved_at              timestamp,
    insured_person_uuid      uuid                                       not null
);

alter table public.ins_hs_claim
    owner to phillip;

create table if not exists public.ins_hs_claim_generate_payment_or_claim_no
(
    id             serial
        constraint ins_hs_claim_generate_payment_no_pk
            primary key,
    policy_id      integer               not null,
    seq_no         varchar               not null,
    payment_no     varchar               not null,
    claim_no       varchar               not null,
    year           varchar default now() not null,
    generate_type  varchar               not null,
    ms_tbl_handler varchar               not null,
    constraint ins_hs_claim_generate_payment_no_pk2
        unique (seq_no, year, generate_type, ms_tbl_handler)
);

alter table public.ins_hs_claim_generate_payment_or_claim_no
    owner to phillip;

create table if not exists public.ins_hs_claim_detail
(
    id                            serial
        constraint ins_hs_claim_detail_no_pk
            primary key,
    status                        char(3)   default 'ACT'::character varying not null,
    version                       integer   default 1                        not null,
    claim_id                      integer                                    not null,
    remark                        text,
    date_of_disability            timestamp,
    date_of_completed_doc         timestamp,
    due_to                        text,
    total_actual_incurred_expense numeric,
    total_maximum_payable         numeric,
    total_non_payable_expense     numeric,
    created_at                    timestamp default now()                    not null,
    created_by                    integer                                    not null,
    updated_at                    timestamp,
    updated_by                    integer,
    approved_by                   integer,
    approved_at                   timestamp,
    approved_status               varchar(5),
    approved_cmt                  text,
    constraint ins_hs_claim_detail_claim_id_version_pk
        unique (claim_id, version)
);

alter table public.ins_hs_claim_detail
    owner to phillip;

create table if not exists public.ins_hs_claim_schema_data
(
    id                      serial
        constraint ins_hs_claim_schema_data_no_pk
            primary key,
    status                  char(3)   default 'ACT'::character varying not null,
    claim_detail_id         integer                                    not null,
    schema_detail_code      varchar                                    not null,
    admission_date          timestamp,
    discharge_date          timestamp,
    number_of_day           integer,
    max_number_of_day       integer,
    schema_name             text                                       not null,
    limit_amount            numeric                                    not null,
    actual_incurred_expense numeric,
    maximum_payable         numeric,
    non_payable_expense     numeric,
    created_at              timestamp default now()                    not null,
    created_by              integer                                    not null,
    updated_at              timestamp,
    updated_by              integer
);

alter table public.ins_hs_claim_schema_data
    owner to phillip;

create table if not exists public.ins_hs_claim_transaction
(
    id                  serial
        constraint ins_hs_claim_transaction_no_pk
            primary key,
    policy_id           integer                  not null,
    data_id             integer                  not null,
    data_detail_id      integer                  not null,
    claim_id            integer                  not null,
    claim_detail_id     integer                  not null,
    payee_id            integer                  not null,
    payment_no          varchar,
    payment_type        varchar,
    total_claim         numeric                  not null,
    status              varchar(3) default 'ACT'::character varying,
    created_at          timestamp  default now() not null,
    created_by          integer                  not null,
    updated_at          timestamp,
    updated_by          integer,
    approved_status     varchar(5),
    approved_at         timestamp,
    approved_by         integer,
    approved_cmt        text,
    insured_person_uuid uuid                     not null
);

alter table public.ins_hs_claim_transaction
    owner to phillip;

create table if not exists public.v_remaining
(
    min numeric
);

alter table public.v_remaining
    owner to phillip;

create table if not exists public.sm_app
(
    id         bigserial
        primary key,
    code       text not null,
    name       text not null,
    status     text not null,
    created_at timestamp(0),
    updated_at timestamp(0),
    created_by text,
    updated_by text
);

alter table public.sm_app
    owner to phillip;

create unique index if not exists sm_app_code_status_unique
    on public.sm_app (code)
    where (status = 'ACT'::text);

create table if not exists public.sm_group
(
    id          bigserial
        primary key,
    code        text not null,
    name        text not null,
    status      text not null,
    created_at  timestamp(0),
    updated_at  timestamp(0),
    is_default  boolean default false,
    created_by  text,
    updated_by  text,
    description text
);

alter table public.sm_group
    owner to phillip;

create unique index if not exists sm_group_code_status_unique
    on public.sm_group (code)
    where (status = 'ACT'::text);

create table if not exists public.sm_org
(
    id          bigserial
        primary key,
    code        text not null,
    name        text not null,
    status      text not null,
    created_at  timestamp(0),
    updated_at  timestamp(0),
    partner_ccy text
);

alter table public.sm_org
    owner to phillip;

create unique index if not exists sm_org_code_status_unique
    on public.sm_org (code)
    where (status = 'ACT'::text);

create table if not exists public.sm_role
(
    id          bigserial
        primary key,
    code        text not null,
    name        text not null,
    status      text not null,
    created_at  timestamp(0),
    updated_at  timestamp(0),
    module      text,
    created_by  text,
    updated_by  text,
    description text
);

alter table public.sm_role
    owner to phillip;

create unique index if not exists sm_role_code_status_unique
    on public.sm_role (code)
    where (status = 'ACT'::text);

create table if not exists public.sm_branch
(
    id              bigserial
        primary key,
    code            text   not null,
    name            text   not null,
    org_id          bigint not null
        constraint sm_branch_org_id_foreign
            references public.sm_org,
    status          text   not null,
    created_at      timestamp(0),
    updated_at      timestamp(0),
    location        text,
    business_hour   text,
    is_full_service boolean,
    constraint sm_branch_code_org_id_status_unique
        unique (code, org_id, status)
);

alter table public.sm_branch
    owner to phillip;

create table if not exists public.sm_default
(
    id          bigserial
        primary key,
    entity_type text   not null,
    entity_id   text   not null,
    org_id      bigint not null
        constraint sm_default_org_id_foreign
            references public.sm_org,
    event       text   not null,
    created_at  timestamp(0),
    updated_at  timestamp(0)
);

alter table public.sm_default
    owner to phillip;

create table if not exists public.sm_function
(
    id         bigserial
        primary key,
    code       text   not null,
    name       text   not null,
    app_id     bigint not null
        constraint sm_function_app_id_foreign
            references public.sm_app,
    status     text   not null,
    created_at timestamp(0),
    updated_at timestamp(0),
    created_by text,
    updated_by text
);

alter table public.sm_function
    owner to phillip;

create unique index if not exists sm_function_code_status_unique
    on public.sm_function (code)
    where (status = 'ACT'::text);

create table if not exists public.sm_group_role
(
    id         bigserial
        primary key,
    group_id   bigint not null
        constraint sm_group_role_group_id_foreign
            references public.sm_group,
    role_id    bigint not null
        constraint sm_group_role_role_id_foreign
            references public.sm_role,
    created_at timestamp(0),
    updated_at timestamp(0)
);

alter table public.sm_group_role
    owner to phillip;

create table if not exists public.sm_permission
(
    id          bigserial
        primary key,
    code        text   not null
        constraint sm_permission_code_unique
            unique,
    name        text   not null,
    function_id bigint not null
        constraint sm_permission_function_id_foreign
            references public.sm_function,
    app_id      bigint not null
        constraint sm_permission_app_id_foreign
            references public.sm_app,
    status      text   not null,
    created_at  timestamp(0),
    updated_at  timestamp(0),
    created_by  text,
    updated_by  text
);

alter table public.sm_permission
    owner to phillip;

create table if not exists public.sm_permission_role
(
    id            bigserial
        primary key,
    permission_id bigint not null
        constraint sm_permission_role_permission_id_foreign
            references public.sm_permission,
    role_id       bigint not null
        constraint sm_permission_role_role_id_foreign
            references public.sm_role,
    created_at    timestamp(0),
    updated_at    timestamp(0)
);

alter table public.sm_permission_role
    owner to phillip;

create table if not exists public.sm_token
(
    id           bigserial
        primary key,
    user_id      integer not null,
    name         text    not null,
    token        text    not null
        constraint sm_token_token_unique
            unique,
    last_used_at timestamp(0),
    expires_at   timestamp(0),
    created_at   timestamp(0),
    updated_at   timestamp(0),
    type         text    not null,
    app_code     text
);

alter table public.sm_token
    owner to phillip;

create table if not exists public.wfl_template_predef_keyword
(
    id          serial
        primary key,
    code        text not null
        unique,
    description text,
    sql_expr    text,
    status      char(3)   default 'ACT'::bpchar,
    created_at  timestamp default now(),
    created_by  text not null,
    updated_at  timestamp,
    updated_by  text
);

alter table public.wfl_template_predef_keyword
    owner to phillip;

create table if not exists public.sys_audit_log
(
    id              bigserial
        primary key,
    action_name     varchar(64)  not null,
    actionable_id   varchar(64)  not null,
    actionable_type varchar(255) not null,
    target_id       varchar(64)  not null,
    target_type     varchar(255) not null,
    model_id        varchar(64),
    model_type      varchar(255) not null,
    original        text,
    changes         text,
    created_by      varchar(32)  not null,
    app_code        varchar(16)  not null,
    status          varchar(16)  not null,
    exception       text,
    created_at      timestamp(0),
    updated_at      timestamp(0),
    ip_address      text
);

alter table public.sys_audit_log
    owner to phillip;

create table if not exists public.my_table
(
    id  serial
        primary key,
    col text
);

alter table public.my_table
    owner to phillip;

create table if not exists public.v_cover_pkg_id
(
    id integer
);

alter table public.v_cover_pkg_id
    owner to phillip;

create table if not exists public.ois_external_task_log
(
    id                serial
        constraint ois_external_task_log_pk
            primary key,
    task_uuid         uuid                    not null,
    task_name         varchar                 not null,
    task_data         text,
    reference_type    varchar                 not null,
    reference_id      integer                 not null,
    status            varchar                 not null,
    error_message     text,
    created_at        timestamp default now() not null,
    last_attempted_at timestamp,
    completed_at      timestamp,
    url               varchar,
    constraint ois_external_task_log_unq
        unique (task_name, reference_type, reference_id)
);

alter table public.ois_external_task_log
    owner to phillip;

create table if not exists public.ins_pa_working_class
(
    id          serial
        constraint ins_pa_working_class_pk
            primary key,
    status      varchar(3) default 'ACT'::character varying not null,
    name        varchar(50)                                 not null,
    description text                                        not null,
    code        varchar(10)                                 not null,
    created_at  timestamp  default now()                    not null,
    created_by  varchar,
    updated_at  timestamp,
    updated_by  varchar
);

alter table public.ins_pa_working_class
    owner to phillip;

create unique index if not exists ins_pa_working_class_code_active_status_uniq
    on public.ins_pa_working_class (code)
    where ((status)::text = 'ACT'::text);

create table if not exists public.ins_pa_data_detail
(
    id                           serial
        constraint ins_pa_data_detail_pk
            primary key,
    status                       varchar(3) default 'ACT'::character varying     not null,
    insured_person_uuid          uuid       default gen_random_uuid()            not null,
    product_code                 varchar(20)                                     not null,
    master_data_type             varchar(10)                                     not null,
    master_data_id               integer                                         not null,
    name                         varchar(100)                                    not null,
    occupation                   varchar,
    gender                       varchar(1),
    date_of_birth                date,
    working_class_code           varchar                                         not null,
    sum_insured                  numeric(15, 2)                                  not null,
    permanent_disablement_amount numeric(15, 2)                                  not null,
    medical_expense_amount       numeric(15, 2)                                  not null,
    endorsement_stage            varchar(50),
    endorsement_state            varchar(50),
    inception_date               date,
    endorsement_e_date           date,
    endos_day_remaining          integer    default 0                            not null,
    claim_request_count          integer    default 0                            not null,
    remark                       varchar,
    previous_id                  integer,
    refund_option                varchar,
    refund_percentage            numeric,
    premium                      numeric,
    premium_amt_bf_refund        numeric,
    custom_refund_amount         numeric,
    created_at                   timestamp  default now()                        not null,
    created_by                   varchar,
    updated_at                   timestamp,
    updated_by                   varchar,
    relationship                 varchar,
    endorsement_option           varchar    default 'DEFAULT'::character varying not null
);

alter table public.ins_pa_data_detail
    owner to phillip;

create index if not exists idx_insured_person_uuid
    on public.ins_pa_data_detail (insured_person_uuid);

create index if not exists idx_product_code
    on public.ins_pa_data_detail (product_code);

create table if not exists public.ins_pa_data_master
(
    id                        serial
        constraint ins_pa_data_master_pk
            primary key,
    status                    varchar(3) default 'ACT'::character varying not null,
    data_type                 varchar(10),
    product_code              varchar(20),
    branch_code               varchar,
    customer_no               varchar,
    coverage_id               integer                                     not null,
    coverage_name             varchar                                     not null,
    insurance_period_type     varchar,
    insurance_period_code     varchar,
    insurance_period_val      numeric,
    total_premium             numeric,
    negotiation_rate          numeric,
    remark                    varchar,
    joint_status              varchar(1),
    insured_name              varchar(100),
    insured_name_kh           varchar(100),
    insured_name_zh           varchar(100),
    business_code             varchar,
    sale_channel              varchar,
    commission_rate           numeric,
    handler_code              varchar,
    warranty                  text,
    warranty_kh               text,
    memorandum                text,
    memorandum_kh             text,
    subjectivity              text,
    subjectivity_kh           text,
    policy_wording_version    varchar,
    previous_id               integer,
    effective_date_from       date,
    effective_date_to         date,
    effective_month           integer,
    effective_day             integer,
    endorsement_e_date        date,
    endos_day_remaining       integer,
    endorsement_type          varchar(50),
    calc_option               varchar,
    surcharge                 numeric    default 0,
    discount                  numeric    default 0,
    insured_person_note       text,
    insured_person_note_kh    text,
    remark_kh                 text,
    refund_option             text,
    refund_percentage         numeric,
    premium_amt_bf_refund     numeric,
    custom_refund_amount      numeric,
    created_at                timestamp  default now()                    not null,
    created_by                varchar,
    updated_at                timestamp,
    updated_by                varchar,
    accumulation_limit_amount numeric
);

alter table public.ins_pa_data_master
    owner to phillip;

create table if not exists public.ins_pa_insured_person_calc_final
(
    id                          serial
        constraint ins_pa_insured_person_final_calc_pk
            primary key,
    status                      varchar(3) default 'ACT'::character varying not null,
    data_id                     integer                                     not null,
    insured_person_aggregate_id integer                                     not null,
    working_class_code          varchar                                     not null,
    coverage_type_id            integer                                     not null,
    base_premium                numeric,
    premium                     numeric,
    total_premium               numeric,
    created_at                  timestamp  default now()                    not null,
    created_by                  varchar,
    updated_at                  timestamp,
    updated_by                  varchar
);

alter table public.ins_pa_insured_person_calc_final
    owner to phillip;

create table if not exists public.ins_pa_quotation
(
    id                serial
        constraint ins_pa_quotation_pk
            primary key,
    quotation_no      varchar(25),
    version           integer   default 0 not null,
    branch_code       varchar(3),
    customer_no       varchar(20),
    product_line_code varchar(25),
    product_code      varchar(5),
    data_id           integer,
    sum_insured       double precision,
    premium           double precision,
    quotation_alt_no  varchar(50),
    status            varchar(3),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10),
    account_code      varchar(6),
    handler_code      varchar(6),
    document_no       varchar(50),
    approved_at       timestamp,
    approved_by       varchar(10),
    approved_status   varchar(5),
    accepted_status   varchar(5),
    accepted_at       timestamp,
    accepted_by       varchar(10),
    accepted_reason   text,
    approved_reason   text
);

alter table public.ins_pa_quotation
    owner to phillip;

create table if not exists public.ins_pa_policy
(
    id                      serial
        constraint ins_pa_policy_pk
            primary key,
    status                  varchar(5)              not null,
    policy_no               varchar(25),
    version                 integer   default 0     not null,
    cycle                   integer   default 0     not null,
    document_no             varchar(25),
    quotation_id            integer,
    branch_code             varchar(3),
    customer_no             varchar(20),
    product_line_code       varchar(25),
    product_code            varchar(5),
    data_id                 integer,
    premium                 numeric,
    premium_adjustment      numeric   default 0     not null,
    policy_alt_no           varchar(50),
    account_code            varchar(6),
    handler_code            varchar(6),
    created_at              timestamp default now() not null,
    created_by              varchar(10),
    updated_at              timestamp,
    updated_by              varchar(10),
    approved_at             timestamp,
    approved_by             varchar(10),
    business_type           text,
    policy_type             text,
    approved_status         varchar(3),
    approved_reason         text,
    endorsement_description text,
    request_amount          numeric,
    renewal_reference_id    integer
);

alter table public.ins_pa_policy
    owner to phillip;

create table if not exists public.ins_pa_insured_person_calc
(
    id                          serial
        constraint ins_pa_insured_person_calc_pk
            primary key,
    status                      varchar(3) default 'ACT'::character varying not null,
    data_id                     integer                                     not null,
    insured_person_aggregate_id integer                                     not null,
    extension_id                integer                                     not null,
    amount                      numeric,
    created_at                  timestamp  default now()                    not null,
    created_by                  varchar,
    updated_at                  timestamp,
    updated_by                  varchar
);

alter table public.ins_pa_insured_person_calc
    owner to phillip;

create table if not exists public.ins_pa_policy_commission_data
(
    id                    serial
        constraint ins_pa_policy_commission_data_pk
            primary key,
    status                varchar(3) default 'ACT'::character varying not null,
    data_id               integer,
    policy_id             integer,
    policy_no             text,
    business_category     text,
    business_code         text,
    gross_written_premium numeric,
    premium_tax_fee_rate  numeric,
    premium_tax_fee       numeric,
    net_written_premium   numeric,
    commission_rate       numeric,
    commission_amount     numeric,
    witholding_tax_rate   numeric,
    witholding_tax        numeric,
    commission_due_amount numeric,
    created_at            timestamp  default now(),
    created_by            varchar(10),
    updated_at            timestamp,
    updated_by            varchar(10)
);

alter table public.ins_pa_policy_commission_data
    owner to phillip;

create table if not exists public.ins_pa_reinsurance_config
(
    id                serial
        constraint ins_pa_reinsurance_config_pk
            primary key,
    status            varchar(3) default 'ACT'::character varying not null,
    product_line_code varchar,
    product_code      varchar,
    reinsurance_type  text,
    reinsurance_code  text,
    partner_code      text,
    start_from        date,
    start_to          date,
    leaf              varchar,
    lvl               integer,
    parent_code       varchar,
    share_basis       varchar,
    uw_year           integer,
    share             numeric,
    amount_cap        numeric,
    ri_commission     numeric,
    tax_fee           numeric,
    created_at        timestamp  default now(),
    created_by        varchar,
    updated_at        timestamp,
    updated_by        varchar
);

alter table public.ins_pa_reinsurance_config
    owner to phillip;

create table if not exists public.ins_pa_reinsurance_data
(
    id                serial
        constraint ins_pa_reinsurance_data_pk
            primary key,
    status            varchar(3) default 'ACT'::character varying not null,
    policy_id         integer,
    data_id           integer,
    product_line_code varchar(25),
    product_code      varchar(5),
    uw_year           integer,
    treaty_code       varchar(50),
    lvl               integer,
    parent_code       varchar(50),
    share             numeric,
    sum_insured       numeric,
    premium           numeric,
    ri_commission     numeric,
    ri_commission_amt numeric,
    tax_fee           numeric,
    tax_fee_amt       numeric,
    net_premium       numeric,
    endorsement_stage text,
    endorsement_state text,
    created_at        timestamp  default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10)
);

alter table public.ins_pa_reinsurance_data
    owner to phillip;

create table if not exists public.ins_hs_policy_endor_commission_hist
(
    id                    serial
        constraint ins_hs_policy_endor_commission_hist_pk
            primary key,
    status                varchar(3) default 'ACT'::character varying not null,
    policy_id             integer,
    endorsement_stage     text,
    endorsement_state     text,
    data_id               integer,
    business_category     text,
    business_code         text,
    gross_written_premium double precision,
    premium_tax_fee_rate  double precision,
    premium_tax_fee       double precision,
    net_written_premium   double precision,
    commission_rate       double precision,
    commission_amount     double precision,
    witholding_tax_rate   double precision,
    witholding_tax        double precision,
    commission_due_amount double precision,
    created_at            timestamp  default now()                    not null,
    created_by            varchar(10),
    updated_at            timestamp,
    updated_by            varchar(10)
);

alter table public.ins_hs_policy_endor_commission_hist
    owner to phillip;

create table if not exists public.ins_pa_policy_endor_commission_hist
(
    id                    serial
        constraint ins_pa_policy_endor_commission_hist_pk
            primary key,
    status                varchar(3) default 'ACT'::character varying not null,
    policy_id             integer,
    endorsement_stage     text,
    endorsement_state     text,
    data_id               integer,
    business_category     text,
    business_code         text,
    gross_written_premium numeric,
    premium_tax_fee_rate  numeric,
    premium_tax_fee       numeric,
    net_written_premium   numeric,
    commission_rate       numeric,
    commission_amount     numeric,
    witholding_tax_rate   numeric,
    witholding_tax        numeric,
    commission_due_amount numeric,
    created_at            timestamp  default now()                    not null,
    created_by            varchar(10),
    updated_at            timestamp,
    updated_by            varchar(10)
);

alter table public.ins_pa_policy_endor_commission_hist
    owner to phillip;

create table if not exists public.ins_pa_policy_invoice_note
(
    id                 serial
        constraint ins_pa_policy_invoice_note_pk
            primary key,
    status             varchar(3) default 'ACT'::character varying not null,
    policy_id          integer                                     not null,
    policy_document_no varchar(20)                                 not null,
    type               varchar(12)                                 not null,
    inv_cdn_no         varchar(15)                                 not null,
    seq_no             varchar(10)                                 not null,
    issue_date         date                                        not null,
    code               varchar(10)                                 not null,
    customer_name      text,
    address            text,
    tin_code           varchar(20),
    product_name       varchar(50),
    insurance_period   text,
    endorsement_e_date date,
    total_premuim      double precision                            not null,
    exch_rate          double precision                            not null,
    created_at         timestamp                                   not null,
    created_by         text,
    updated_at         timestamp,
    updated_by         text
);

alter table public.ins_pa_policy_invoice_note
    owner to phillip;

create table if not exists public.ins_tv_zone
(
    id          serial
        primary key,
    created_at  timestamp  default now()                    not null,
    updated_at  timestamp,
    created_by  varchar(50)                                 not null,
    updated_by  varchar(50),
    status      varchar(3) default 'ACT'::character varying not null,
    name        varchar(50)                                 not null,
    code        varchar(50)                                 not null
        unique,
    description text
);

alter table public.ins_tv_zone
    owner to phillip;

create table if not exists public.ins_tv_plan
(
    id              serial
        primary key,
    created_at      timestamp  default now()                    not null,
    updated_at      timestamp,
    created_by      varchar(50)                                 not null,
    updated_by      varchar(50),
    status          varchar(3) default 'ACT'::character varying not null,
    name            varchar(100)                                not null,
    code            varchar(50)                                 not null
        unique,
    aggregate_limit integer                                     not null,
    description     text
);

alter table public.ins_tv_plan
    owner to phillip;

create table if not exists public.ins_tv_coverage_duration
(
    id         integer    default nextval('ins_tv_duration_range_id_seq'::regclass) not null
        constraint ins_tv_duration_range_pkey
            primary key,
    created_at timestamp  default now()                                             not null,
    updated_at timestamp,
    created_by varchar(50)                                                          not null,
    updated_by varchar(50),
    status     varchar(3) default 'ACT'::character varying                          not null,
    min_day    smallint                                                             not null,
    max_day    smallint                                                             not null,
    name       varchar(100),
    code       varchar(50)
        unique
);

alter table public.ins_tv_coverage_duration
    owner to phillip;

create table if not exists public.test_duration_plan_zone_package
(
    id                integer    default nextval('ins_tv_duration_plan_zone_package_id_seq'::regclass) not null
        constraint ins_tv_duration_plan_zone_package_pkey
            primary key,
    duration_range_id integer                                                                          not null,
    zone_id           integer                                                                          not null,
    package_id        integer                                                                          not null,
    created_at        timestamp  default now()                                                         not null,
    updated_at        timestamp,
    created_by        varchar(50)                                                                      not null,
    updated_by        varchar(50),
    status            varchar(3) default 'ACT'::character varying                                      not null,
    constraint ins_tv_duration_plan_zone_pac_duration_range_id_zone_id_pac_key
        unique (duration_range_id, zone_id, package_id)
);

alter table public.test_duration_plan_zone_package
    owner to phillip;

create table if not exists public.test_ins_tv_premium_rate
(
    id                            serial
        primary key,
    created_at                    timestamp  default now()                    not null,
    updated_at                    timestamp,
    created_by                    varchar(50)                                 not null,
    updated_by                    varchar(50),
    status                        varchar(3) default 'ACT'::character varying not null,
    plan_package_zone_duration_id integer                                     not null
        constraint unique_plan_package_zone_duration
            unique,
    rate_value                    numeric(10, 2)                              not null,
    rate_type                     varchar(10)                                 not null
        constraint test_ins_tv_premium_rate_rate_type_check
            check ((rate_type)::text = ANY ((ARRAY ['FIXED'::character varying, 'PER_DAY'::character varying])::text[]))
);

alter table public.test_ins_tv_premium_rate
    owner to phillip;

create table if not exists public.ins_tv_premium_rate
(
    id                     serial
        primary key,
    created_at             timestamp  default now(),
    updated_at             timestamp,
    created_by             varchar(50)                                 not null,
    updated_by             varchar(50),
    status                 varchar(3) default 'ACT'::character varying not null,
    rate_value             numeric(10, 2)                              not null,
    rate_type              varchar(10)                                 not null
        constraint ins_tv_premium_rate_rate_type_check
            check ((rate_type)::text = ANY
                   ((ARRAY ['FIXED'::character varying, 'PER_DAY'::character varying])::text[])),
    plan_code              varchar(50),
    package_code           varchar(50),
    zone_code              varchar(50),
    coverage_duration_code varchar(50),
    constraint unique_premium_rate
        unique (plan_code, package_code, zone_code, coverage_duration_code)
);

alter table public.ins_tv_premium_rate
    owner to phillip;

create table if not exists public.ins_pa_coverage_type
(
    id          serial
        constraint ins_pa_coverage_type_pk
            primary key,
    status      varchar(3) default 'ACT'::character varying not null,
    code        varchar                                     not null,
    name        varchar(50)                                 not null,
    description text,
    created_at  timestamp  default now()                    not null,
    created_by  varchar,
    updated_at  timestamp,
    updated_by  varchar
);

alter table public.ins_pa_coverage_type
    owner to phillip;

create unique index if not exists ins_pa_coverage_type_code_active_status_uniq
    on public.ins_pa_coverage_type (code)
    where ((status)::text = 'ACT'::text);

create table if not exists public.ins_pa_coverage_benefit
(
    id          serial
        constraint ins_pa_coverage_benefit_pk
            primary key,
    status      varchar(3) default 'ACT'::character varying not null,
    code        varchar                                     not null,
    name        varchar(50)                                 not null,
    description text,
    amount_type varchar                                     not null,
    created_at  timestamp  default now()                    not null,
    created_by  varchar,
    updated_at  timestamp,
    updated_by  varchar
);

alter table public.ins_pa_coverage_benefit
    owner to phillip;

create unique index if not exists ins_pa_coverage_benefit_code_active_status_uniq
    on public.ins_pa_coverage_benefit (code)
    where ((status)::text = 'ACT'::text);

create table if not exists public.ins_tv_policy
(
    id                      serial
        constraint ins_tv_policy_pk
            primary key,
    status                  varchar(5)              not null,
    policy_no               varchar(25),
    version                 integer   default 0,
    cycle                   integer   default 0     not null,
    document_no             varchar(25),
    quotation_id            integer,
    branch_code             varchar(3),
    customer_no             varchar(20),
    product_line_code       varchar(25),
    product_code            varchar(5),
    data_id                 integer,
    premium                 numeric,
    premium_adjustment      numeric   default 0     not null,
    policy_alt_no           varchar(50),
    account_code            varchar(6),
    handler_code            varchar(6),
    created_at              timestamp default now() not null,
    created_by              varchar(10),
    updated_at              timestamp,
    updated_by              varchar(10),
    approved_at             timestamp,
    approved_by             varchar(10),
    business_type           text,
    policy_type             text,
    approved_status         varchar(3),
    approved_reason         text,
    endorsement_description text,
    request_amount          numeric,
    renewal_reference_id    integer
);

alter table public.ins_tv_policy
    owner to phillip;

create index if not exists idx_ins_tv_policy_policy_no
    on public.ins_tv_policy (policy_no);

create index if not exists idx_ins_tv_policy_customer_no
    on public.ins_tv_policy (customer_no);

create table if not exists public.ins_pa_extension_selection
(
    id                    serial
        constraint ins_pa_extension_selection_pk
            primary key,
    status                varchar(3) default 'ACT'::character varying not null,
    data_id               integer                                     not null,
    extension_id          integer                                     not null,
    extension_code        varchar                                     not null,
    extension_name        varchar                                     not null,
    extension_description varchar,
    is_selected           boolean    default false                    not null,
    amount_type           varchar,
    rating                numeric,
    created_at            timestamp  default now()                    not null,
    created_by            varchar,
    updated_at            timestamp,
    updated_by            varchar
);

alter table public.ins_pa_extension_selection
    owner to phillip;

create table if not exists public.v_commission_due
(
    commission_due double precision
);

alter table public.v_commission_due
    owner to phillip;

create table if not exists public.ins_tv_quotation
(
    id                serial
        constraint ins_tv_quotation_pk
            primary key,
    quotation_no      varchar(25),
    version           integer,
    branch_code       varchar(3),
    customer_no       varchar(20),
    product_line_code varchar(25),
    product_code      varchar(5),
    data_id           integer,
    sum_insured       double precision,
    premium           double precision,
    quotation_alt_no  varchar(50),
    status            varchar(3),
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10),
    account_code      varchar(6),
    handler_code      varchar(6),
    document_no       varchar(50),
    approved_at       timestamp,
    approved_by       varchar(10),
    approved_status   varchar(5),
    accepted_status   varchar(5),
    accepted_at       timestamp,
    accepted_by       varchar(10),
    accepted_reason   text,
    approved_reason   text
);

alter table public.ins_tv_quotation
    owner to phillip;

create table if not exists public.package
(
    id           serial
        primary key,
    created_at   timestamp  default now()                    not null,
    updated_at   timestamp,
    created_by   varchar(50)                                 not null,
    updated_by   varchar(50),
    status       varchar(3) default 'ACT'::character varying not null,
    name         varchar(100)                                not null,
    code         varchar(50),
    package_type varchar(10)                                 not null,
    group_type   varchar(10)
);

alter table public.package
    owner to phillip;

create table if not exists public.plan_package
(
    plan_id      integer                                     not null,
    package_id   integer                                     not null,
    plan_code    varchar(50)                                 not null,
    package_code varchar(50)                                 not null,
    status       varchar(3) default 'ACT'::character varying not null,
    created_at   timestamp  default now()                    not null,
    updated_at   timestamp,
    created_by   varchar(50)                                 not null,
    updated_by   varchar(50),
    primary key (plan_id, package_id)
);

alter table public.plan_package
    owner to phillip;

create table if not exists public.ins_tv_package
(
    id               serial
        primary key,
    created_at       timestamp  default now()                    not null,
    updated_at       timestamp,
    created_by       varchar(50)                                 not null,
    updated_by       varchar(50),
    status           varchar(3) default 'ACT'::character varying not null,
    name             varchar(100)                                not null,
    code             varchar(50)                                 not null
        unique,
    package_type     varchar(10)                                 not null,
    group_type       varchar(10),
    min_child        integer,
    max_child        integer,
    total_min_people integer,
    total_max_people integer
);

alter table public.ins_tv_package
    owner to phillip;

create table if not exists public.ins_pa_insured_person_aggregate
(
    id                           serial
        constraint ins_pa_insured_person_aggregate_pk
            primary key,
    status                       varchar(3) default 'ACT'::character varying not null,
    previous_id                  integer,
    data_id                      integer                                     not null,
    insured_person_no            integer,
    working_class_id             integer                                     not null,
    working_class_code           varchar                                     not null,
    sum_insured                  numeric,
    permanent_disablement_amount numeric,
    medical_expense_amount       numeric,
    surcharge                    numeric    default 0,
    discount                     numeric    default 0,
    created_at                   timestamp  default now()                    not null,
    created_by                   varchar,
    updated_at                   timestamp,
    updated_by                   varchar
);

alter table public.ins_pa_insured_person_aggregate
    owner to phillip;

create table if not exists public.ins_tv_data_detail
(
    id                    serial
        primary key,
    status                varchar(3) default 'ACT'::character varying not null,
    insured_person_uuid   uuid       default gen_random_uuid()        not null,
    product_code          varchar(20)                                 not null,
    master_data_type      varchar(10)                                 not null,
    master_data_id        integer                                     not null,
    name                  varchar(100)                                not null,
    occupation            varchar,
    gender                varchar(1),
    date_of_birth         date                                        not null,
    passport              varchar(50)                                 not null,
    is_child              boolean    default false                    not null,
    plan_code             varchar(50)                                 not null,
    endorsement_stage     varchar(50),
    endorsement_state     varchar(50),
    inception_date        date,
    endorsement_e_date    date,
    endos_day_remaining   integer    default 0,
    claim_request_count   integer    default 0,
    remark                varchar,
    previous_id           integer,
    refund_option         varchar,
    refund_percentage     numeric,
    premium               numeric    default 0,
    premium_amt_bf_refund numeric,
    custom_refund_amount  numeric,
    created_at            timestamp  default now()                    not null,
    created_by            varchar,
    updated_at            timestamp,
    updated_by            varchar
);

alter table public.ins_tv_data_detail
    owner to phillip;

create table if not exists public.ins_pa_class_coverage_rate
(
    id                    serial
        constraint ins_pa_class_coverage_rate_pk
            primary key,
    status                varchar(3) default 'ACT'::character varying not null,
    coverage_code         varchar                                     not null,
    class_code            varchar                                     not null,
    coverage_benefit_code varchar                                     not null,
    rate                  numeric,
    created_at            timestamp  default now()                    not null,
    created_by            varchar,
    updated_at            timestamp,
    updated_by            varchar
);

alter table public.ins_pa_class_coverage_rate
    owner to phillip;

create index if not exists ins_pa_class_coverage_rate_config_idx
    on public.ins_pa_class_coverage_rate (coverage_code, class_code, coverage_benefit_code);

create table if not exists public.ins_tv_plan_zone_detail
(
    id          serial
        primary key,
    created_at  timestamp  default now(),
    updated_at  timestamp,
    created_by  varchar(50) not null,
    updated_by  varchar(50),
    status      varchar(3) default 'ACT'::character varying,
    plan_code   varchar(50),
    zone_code   varchar(50),
    min_premium numeric     not null,
    constraint ins_tv_plan_zone_details_plan_code_zone_code_key
        unique (plan_code, zone_code)
);

alter table public.ins_tv_plan_zone_detail
    owner to phillip;

create table if not exists public.ins_pa_data_clause
(
    id         bigserial
        constraint ins_pa_data_clause_pk
            primary key,
    status     varchar(3) default 'ACT'::character varying,
    created_at timestamp  default now(),
    created_by varchar(10),
    updated_at timestamp,
    updated_by varchar(10),
    data_id    integer,
    clause_id  integer
);

alter table public.ins_pa_data_clause
    owner to phillip;

create table if not exists public.ins_tv_coverage
(
    id                    serial
        primary key,
    created_at            timestamp  default now(),
    updated_at            timestamp,
    created_by            varchar(50)                                 not null,
    updated_by            varchar(50),
    status                varchar(3) default 'ACT'::character varying not null,
    code                  varchar(50)                                 not null
        unique,
    name                  varchar(225)                                not null,
    name_kh               varchar(255),
    category              varchar(50),
    seq_no                smallint,
    use_time_unit         boolean    default false                    not null,
    standard_limit        numeric,
    deluxe_limit          numeric,
    executive_limit       numeric,
    unit_type             varchar,
    standard_unit_amount  numeric,
    deluxe_unit_amount    numeric,
    executive_unit_amount numeric
);

alter table public.ins_tv_coverage
    owner to phillip;

create table if not exists public.ins_tv_coverage_data
(
    id                    serial
        primary key,
    created_at            timestamp  default now(),
    updated_at            timestamp,
    created_by            varchar(50)                                 not null,
    updated_by            varchar(50),
    status                varchar(3) default 'ACT'::character varying not null,
    data_id               integer                                     not null,
    name                  varchar(225)                                not null,
    name_kh               varchar(255),
    category              varchar(50),
    seq_no                smallint,
    use_time_unit         boolean    default false                    not null,
    standard_limit        numeric,
    deluxe_limit          numeric,
    executive_limit       numeric,
    unit_type             varchar,
    standard_unit_amount  numeric,
    deluxe_unit_amount    numeric,
    executive_unit_amount numeric,
    data_type             varchar(50),
    code                  varchar(50)                                 not null
);

alter table public.ins_tv_coverage_data
    owner to phillip;

create table if not exists public.ins_tv_policy_commission_data
(
    id                    serial
        constraint ins_tv_policy_commission_data_pk
            primary key,
    status                varchar(3) default 'ACT'::character varying not null,
    data_id               integer,
    policy_id             integer,
    policy_no             text,
    business_category     text,
    business_code         text,
    gross_written_premium double precision,
    premium_tax_fee_rate  double precision,
    premium_tax_fee       double precision,
    net_written_premium   double precision,
    commission_rate       double precision,
    commission_amount     double precision,
    witholding_tax_rate   double precision,
    witholding_tax        double precision,
    commission_due_amount double precision,
    created_at            timestamp  default now(),
    created_by            varchar(10),
    updated_at            timestamp,
    updated_by            varchar(10)
);

alter table public.ins_tv_policy_commission_data
    owner to phillip;

create table if not exists public.ins_pa_claim
(
    id                   serial
        constraint ins_pa_claim_pk
            primary key,
    status               char(3)    default 'ACT'::character varying not null,
    created_at           timestamp  default now()                    not null,
    created_by           varchar                                     not null,
    updated_at           timestamp,
    updated_by           varchar,
    approved_status      varchar(5) default 'PND'::character varying not null,
    approved_by          varchar,
    approved_cmt         text,
    approved_at          timestamp,
    seq_no               varchar                                     not null,
    claim_no             text                                        not null
        constraint ins_hs_claim_unq
            unique,
    policy_id            integer                                     not null,
    data_id              integer                                     not null,
    data_detail_id       integer                                     not null,
    insured_person_uuid  uuid                                        not null,
    occupation           varchar,
    remark               text,
    date_of_loss         timestamp                                   not null,
    notification_date    timestamp                                   not null,
    location_of_loss     varchar                                     not null,
    loss_description     text,
    total_reserve_amount numeric,
    processing_month     date       default now()                    not null,
    insured_period_from  date                                        not null,
    insured_period_to    date                                        not null,
    is_closed            boolean    default false                    not null,
    original_claim_id    integer,
    previous_claim_id    integer,
    reopen_version       integer    default 1                        not null
);

alter table public.ins_pa_claim
    owner to phillip;

create index if not exists ins_pa_claim_status_is_closed_insured_person_uuid_index
    on public.ins_pa_claim (status, is_closed, insured_person_uuid);

create table if not exists public.ins_pa_claim_generate_payment_or_claim_no
(
    id             serial
        constraint ins_pa_claim_generate_payment_no_pk
            primary key,
    policy_id      integer               not null,
    seq_no         varchar               not null,
    payment_no     varchar               not null,
    claim_no       varchar               not null,
    year           varchar default now() not null,
    generate_type  varchar               not null,
    ms_tbl_handler varchar               not null,
    constraint ins_pa_claim_generate_payment_no_pk2
        unique (seq_no, year, generate_type, ms_tbl_handler)
);

alter table public.ins_pa_claim_generate_payment_or_claim_no
    owner to phillip;

create table if not exists public.ins_pa_claim_detail
(
    id                    bigserial
        constraint ins_pa_claim_detail_pk
            primary key,
    status                char(3)   default 'ACT'::character varying not null,
    created_at            timestamp default now()                    not null,
    created_by            varchar                                    not null,
    updated_at            timestamp,
    updated_by            varchar,
    claim_id              integer                                    not null,
    coverage_benefit_id   integer,
    coverage_benefit_code varchar,
    cause_of_accident     varchar                                    not null,
    reserve_amount        numeric
);

alter table public.ins_pa_claim_detail
    owner to phillip;

create table if not exists public.ins_tv_reinsurance_data
(
    id                serial
        primary key,
    status            varchar(3),
    policy_id         integer,
    data_id           integer,
    product_line_code varchar(25),
    product_code      varchar(5),
    uw_year           integer,
    treaty_code       varchar(50),
    lvl               integer,
    parent_code       varchar(50),
    share             numeric,
    sum_insured       numeric,
    premium           numeric,
    ri_commission     numeric,
    ri_commission_amt numeric,
    tax_fee           numeric,
    tax_fee_amt       numeric,
    net_premium       numeric,
    endorsement_stage text,
    endorsement_state text,
    created_at        timestamp default now(),
    created_by        varchar(10),
    updated_at        timestamp,
    updated_by        varchar(10)
);

alter table public.ins_tv_reinsurance_data
    owner to phillip;

create table if not exists public.ins_tv_reinsurance_config
(
    id                serial
        primary key,
    status            varchar(3),
    product_line_code varchar,
    product_code      varchar,
    reinsurance_type  text,
    reinsurance_code  text,
    partner_code      text,
    start_from        date,
    start_to          date,
    leaf              varchar,
    lvl               integer,
    parent_code       varchar,
    share_basis       varchar,
    uw_year           integer,
    share             numeric,
    amount_cap        numeric,
    ri_commission     numeric,
    tax_fee           numeric,
    created_at        timestamp default now(),
    created_by        varchar,
    updated_at        timestamp,
    updated_by        varchar
);

alter table public.ins_tv_reinsurance_config
    owner to phillip;

create table if not exists public.ins_tv_data_master
(
    id                       serial
        primary key,
    status                   varchar(3) default 'ACT'::character varying not null,
    data_type                varchar(10),
    product_code             varchar(20),
    branch_code              varchar,
    customer_no              varchar,
    package_code             varchar                                     not null,
    itinerary                varchar                                     not null,
    premium_ref_country_code varchar(3),
    premium_ref_zone_code    varchar(50),
    insurance_period_type    varchar,
    insurance_period_code    varchar,
    insurance_period_val     numeric,
    total_premium            numeric    default 0,
    negotiation_rate         numeric,
    remark                   varchar,
    joint_status             varchar(1),
    insured_name             varchar(100),
    insured_name_kh          varchar(100),
    insured_name_zh          varchar(100),
    business_code            varchar,
    sale_channel             varchar,
    commission_rate          numeric,
    handler_code             varchar,
    warranty                 text,
    warranty_kh              text,
    memorandum               text,
    memorandum_kh            text,
    subjectivity             text,
    subjectivity_kh          text,
    policy_wording_version   varchar,
    previous_id              integer,
    effective_date_from      date,
    effective_date_to        date,
    effective_month          double precision,
    effective_day            double precision,
    endorsement_e_date       date,
    endos_day_remaining      integer,
    endorsement_type         varchar(50),
    calc_option              varchar,
    surcharge                numeric    default 0,
    discount                 numeric    default 0,
    accumulation_limit       numeric                                     not null,
    insured_person_note      text,
    insured_person_note_kh   text,
    remark_kh                text,
    refund_option            text,
    refund_percentage        numeric,
    premium_amt_bf_refund    numeric,
    custom_refund_amount     numeric,
    created_at               timestamp  default now()                    not null,
    created_by               varchar,
    updated_at               timestamp,
    updated_by               varchar
);

alter table public.ins_tv_data_master
    owner to phillip;

create table if not exists public.ins_pa_claim_schema
(
    id                            serial
        constraint ins_pa_claim_schema_pk
            primary key,
    status                        char(3)    default 'ACT'::character varying not null,
    created_at                    timestamp  default now()                    not null,
    created_by                    varchar                                     not null,
    updated_at                    timestamp,
    updated_by                    varchar,
    approved_status               varchar(5) default 'PND'::character varying not null,
    approved_by                   varchar,
    approved_at                   timestamp,
    approved_cmt                  text,
    version                       integer    default 1                        not null,
    claim_id                      integer                                     not null,
    remark                        text,
    date_of_completed_doc         timestamp,
    total_actual_incurred_expense numeric,
    total_maximum_payable         numeric,
    total_non_payable_expense     numeric,
    constraint ins_pa_claim_schema_claim_id_version_pk
        unique (claim_id, version)
);

alter table public.ins_pa_claim_schema
    owner to phillip;

create table if not exists public.ins_pa_claim_schema_detail
(
    id                      serial
        constraint ins_pa_claim_schema_detail_pk
            primary key,
    status                  char(3)   default 'ACT'::character varying not null,
    created_at              timestamp default now()                    not null,
    created_by              varchar                                    not null,
    updated_at              timestamp,
    updated_by              varchar,
    claim_schema_id         integer                                    not null,
    claim_detail_id         integer                                    not null,
    limit_amount            numeric                                    not null,
    actual_incurred_expense numeric,
    maximum_payable         numeric,
    non_payable_expense     numeric
);

alter table public.ins_pa_claim_schema_detail
    owner to phillip;

create table if not exists public.ins_translation
(
    id                serial
        constraint ins_translation_pk
            primary key,
    status            char(3)   default 'ACT'::character varying,
    created_at        timestamp default now(),
    created_by        text,
    updated_at        timestamp,
    updated_by        text,
    product_line_code varchar not null,
    key               text,
    lang_code         char(2),
    translation_value text,
    field_name        text,
    table_name        text
);

alter table public.ins_translation
    owner to phillip;

create unique index if not exists ois_common_translation_unq
    on public.ins_translation (product_line_code, key, lang_code, table_name, field_name)
    where (status = 'ACT'::bpchar);

create table if not exists public.ins_tv_data_clause
(
    id         bigserial
        constraint ins_tv_data_clause_pk
            primary key,
    created_at timestamp  default now(),
    created_by varchar(10),
    updated_at timestamp,
    updated_by varchar(10),
    status     varchar(3) default 'ACT'::character varying,
    data_id    integer,
    clause_id  integer
);

alter table public.ins_tv_data_clause
    owner to phillip;

create table if not exists public.ins_tv_policy_invoice_note
(
    id                 serial
        constraint ins_tv_policy_invoice_note_pk
            primary key,
    status             varchar(3) default 'ACT'::character varying not null,
    policy_id          integer                                     not null,
    policy_document_no varchar(20)                                 not null,
    type               varchar(12)                                 not null,
    inv_cdn_no         varchar(15)                                 not null,
    seq_no             varchar(10)                                 not null,
    issue_date         date                                        not null,
    code               varchar(10)                                 not null,
    customer_name      text,
    address            text,
    tin_code           varchar(20),
    product_name       varchar(50),
    insurance_period   text,
    endorsement_e_date date,
    total_premuim      double precision                            not null,
    exch_rate          double precision                            not null,
    created_at         timestamp  default now()                    not null,
    created_by         text,
    updated_at         timestamp,
    updated_by         text
);

alter table public.ins_tv_policy_invoice_note
    owner to phillip;

create table if not exists public.ins_tv_policy_endor_commission_hist
(
    id                    serial
        constraint ins_tv_policy_endor_commission_hist_pk
            primary key,
    status                varchar(3) default 'ACT'::character varying not null,
    policy_id             integer,
    endorsement_stage     text,
    endorsement_state     text,
    data_id               integer,
    business_category     text,
    business_code         text,
    gross_written_premium double precision,
    premium_tax_fee_rate  double precision,
    premium_tax_fee       double precision,
    net_written_premium   double precision,
    commission_rate       double precision,
    commission_amount     double precision,
    witholding_tax_rate   double precision,
    witholding_tax        double precision,
    commission_due_amount double precision,
    created_at            timestamp  default now()                    not null,
    created_by            varchar(10),
    updated_at            timestamp,
    updated_by            varchar(10)
);

alter table public.ins_tv_policy_endor_commission_hist
    owner to phillip;

create table if not exists public.ins_pa_extension_option
(
    id          serial
        constraint ins_pa_extension_option_pk
            primary key,
    status      varchar(3) default 'ACT'::character varying not null,
    created_at  timestamp  default now()                    not null,
    created_by  varchar,
    updated_at  timestamp,
    updated_by  varchar,
    type        varchar                                     not null,
    code        varchar                                     not null,
    name        varchar                                     not null,
    description text
);

alter table public.ins_pa_extension_option
    owner to phillip;

create unique index if not exists ins_pa_extension_option_code_active_status_uniq
    on public.ins_pa_extension_option (code)
    where ((status)::text = 'ACT'::text);

create table if not exists public.ins_tv_claim
(
    id                   serial
        constraint ins_tv_claim_pk
            primary key,
    status               char(3)    default 'ACT'::character varying not null,
    created_at           timestamp  default now()                    not null,
    created_by           varchar                                     not null,
    updated_at           timestamp,
    updated_by           varchar,
    approved_status      varchar(5) default 'PND'::character varying not null,
    approved_by          varchar,
    approved_cmt         text,
    approved_at          timestamp,
    seq_no               varchar                                     not null,
    claim_no             text                                        not null
        constraint ins_tv_claim_unq
            unique,
    policy_id            integer                                     not null,
    data_id              integer                                     not null,
    data_detail_id       integer                                     not null,
    insured_person_uuid  uuid                                        not null,
    occupation           varchar,
    remark               text,
    date_of_loss         timestamp                                   not null,
    notification_date    timestamp                                   not null,
    location_of_loss     varchar                                     not null,
    loss_description     text,
    total_reserve_amount numeric,
    processing_month     date       default now()                    not null,
    insured_period_from  date                                        not null,
    insured_period_to    date                                        not null,
    deductible           numeric                                     not null,
    is_closed            boolean    default false                    not null,
    original_claim_id    integer,
    previous_claim_id    integer,
    reopen_version       integer    default 1                        not null,
    net_reserved_amount  numeric
);

alter table public.ins_tv_claim
    owner to phillip;

create table if not exists public.ins_tv_claim_schema
(
    id                            serial
        constraint ins_tv_claim_schema_pk
            primary key,
    status                        char(3)    default 'ACT'::character varying not null,
    created_at                    timestamp  default now()                    not null,
    created_by                    varchar                                     not null,
    updated_at                    timestamp,
    updated_by                    varchar,
    approved_status               varchar(5) default 'PND'::character varying not null,
    approved_by                   varchar,
    approved_at                   timestamp,
    approved_cmt                  text,
    version                       integer    default 1                        not null,
    claim_id                      integer                                     not null,
    remark                        text,
    date_of_completed_doc         timestamp,
    total_actual_incurred_expense numeric,
    total_maximum_payable         numeric,
    total_non_payable_expense     numeric,
    total_amount_due              numeric,
    constraint ins_tv_claim_schema_claim_id_version_pk
        unique (claim_id, version)
);

alter table public.ins_tv_claim_schema
    owner to phillip;

create table if not exists public.ins_tv_claim_schema_detail
(
    id                      serial
        constraint ins_tv_claim_schema_detail_pk
            primary key,
    status                  char(3)   default 'ACT'::character varying not null,
    created_at              timestamp default now()                    not null,
    created_by              varchar                                    not null,
    updated_at              timestamp,
    updated_by              varchar,
    claim_schema_id         integer                                    not null,
    claim_detail_id         integer                                    not null,
    limit_amount            numeric                                    not null,
    actual_incurred_expense numeric,
    maximum_payable         numeric,
    non_payable_expense     numeric
);

alter table public.ins_tv_claim_schema_detail
    owner to phillip;

create table if not exists public.ins_tv_claim_generate_payment_or_claim_no
(
    id             serial
        constraint ins_tv_claim_generate_payment_no_pk
            primary key,
    policy_id      integer               not null,
    seq_no         varchar               not null,
    payment_no     varchar               not null,
    claim_no       varchar               not null,
    year           varchar default now() not null,
    generate_type  varchar               not null,
    ms_tbl_handler varchar               not null,
    constraint ins_tv_claim_generate_payment_no_pk2
        unique (seq_no, year, generate_type, ms_tbl_handler)
);

alter table public.ins_tv_claim_generate_payment_or_claim_no
    owner to phillip;

create table if not exists public.temp1_sm_app
(
    id         bigint default nextval('sm_app_id_seq'::regclass) not null
        primary key,
    code       text                                              not null
        unique,
    name       text                                              not null,
    status     text                                              not null,
    created_at timestamp(0),
    updated_at timestamp(0)
);

alter table public.temp1_sm_app
    owner to phillip;

create table if not exists public.ins_pa_claim_payment_detail
(
    id                     serial,
    status                 varchar(3) default 'ACT'::character varying,
    created_at             timestamp  default now() not null,
    created_by             varchar                  not null,
    updated_at             timestamp,
    updated_by             varchar,
    payment_id             integer                  not null,
    payment_no             varchar,
    claim_schema_detail_id integer                  not null,
    payee_id               integer                  not null,
    payment_method         varchar                  not null,
    paid_amount            numeric                  not null,
    remaining_balance      numeric                  not null,
    remark                 text
);

alter table public.ins_pa_claim_payment_detail
    owner to phillip;

create table if not exists public.ins_pa_claim_payment
(
    id              serial
        constraint ins_pa_claim_payment_no_pk
            primary key,
    status          varchar(3) default 'ACT'::character varying,
    created_at      timestamp  default now()                    not null,
    created_by      varchar                                     not null,
    updated_at      timestamp,
    updated_by      varchar,
    approved_status varchar(5) default 'PND'::character varying not null,
    approved_at     timestamp,
    approved_by     varchar,
    approved_cmt    text,
    claim_id        integer                                     not null,
    schema_id       integer                                     not null,
    payment_type    varchar,
    total_due       numeric                                     not null,
    total_paid      numeric                                     not null
);

alter table public.ins_pa_claim_payment
    owner to phillip;

create table if not exists public.ins_tv_country
(
    id         serial
        primary key,
    created_at timestamp  default now()                    not null,
    updated_at timestamp,
    created_by varchar(50)                                 not null,
    updated_by varchar(50),
    status     varchar(3) default 'ACT'::character varying not null,
    code       varchar(3)                                  not null
        unique,
    name       varchar(100)                                not null,
    zone_code  varchar(50)
);

alter table public.ins_tv_country
    owner to phillip;

create table if not exists public.ins_tv_deductible
(
    id         serial
        primary key,
    created_at timestamp  default now(),
    updated_at timestamp,
    created_by varchar(50)                                 not null,
    updated_by varchar(50),
    status     varchar(3) default 'ACT'::character varying not null,
    type       varchar(50)                                 not null,
    value      numeric                                     not null,
    label      varchar(255)                                not null,
    label_kh   varchar(255)                                not null,
    ccy        varchar(3) default 'USD'::character varying
);

alter table public.ins_tv_deductible
    owner to phillip;

create table if not exists public.ins_tv_deductible_data
(
    id         serial
        primary key,
    created_at timestamp  default now(),
    updated_at timestamp,
    created_by varchar(50)                                 not null,
    updated_by varchar(50),
    status     varchar(3) default 'ACT'::character varying not null,
    data_id    integer                                     not null,
    data_type  varchar(25),
    type       varchar                                     not null,
    value      numeric                                     not null,
    label      varchar(255)                                not null,
    label_kh   varchar(255)                                not null,
    ccy        varchar(3) default 'USD'::character varying
);

alter table public.ins_tv_deductible_data
    owner to phillip;

create table if not exists public.ins_tv_claim_detail
(
    id               bigserial
        constraint ins_tv_claim_detail_pk
            primary key,
    status           char(3)   default 'ACT'::character varying not null,
    created_at       timestamp default now()                    not null,
    created_by       varchar                                    not null,
    updated_at       timestamp,
    updated_by       varchar,
    claim_id         integer                                    not null,
    coverage_code    varchar                                    not null,
    cause            varchar                                    not null,
    reserve_amount   numeric,
    coverage_data_id integer                                    not null
);

alter table public.ins_tv_claim_detail
    owner to phillip;

create table if not exists public.sm_user
(
    id             bigint     default nextval('sm_user_id_seq1'::regclass) not null
        constraint pk_user_id
            primary key,
    status         varchar(3) default 'ACT'::character varying             not null,
    created_at     timestamp  default now()                                not null,
    created_by     varchar,
    updated_at     timestamp,
    updated_by     varchar,
    username       text                                                    not null,
    email          text,
    full_name      text,
    password       text,
    remember_token text,
    authenticator  text       default 'laravel'::text                      not null,
    home_branch    text,
    login_attempts integer    default 0,
    last_active    timestamp(0),
    employee_code  text,
    report_to      integer
);

alter table public.sm_user
    owner to phillip;

create table if not exists public.sm_user_branch
(
    id         bigserial
        primary key,
    branch_id  bigint not null
        constraint sm_user_branch_branch_id_foreign
            references public.sm_branch,
    user_id    bigint not null
        constraint sm_user_branch_user_id_foreign
            references public.sm_user,
    created_at timestamp(0),
    updated_at timestamp(0)
);

alter table public.sm_user_branch
    owner to phillip;

create table if not exists public.sm_user_group
(
    id         bigserial
        primary key,
    group_id   bigint not null
        constraint sm_user_group_group_id_foreign
            references public.sm_group,
    user_id    bigint not null
        constraint sm_user_group_user_id_foreign
            references public.sm_user,
    created_at timestamp(0),
    updated_at timestamp(0)
);

alter table public.sm_user_group
    owner to phillip;

create table if not exists public.sm_user_org
(
    id         bigserial
        primary key,
    org_id     bigint not null
        constraint sm_user_org_org_id_foreign
            references public.sm_org,
    user_id    bigint not null
        constraint sm_user_org_user_id_foreign
            references public.sm_user,
    created_at timestamp(0),
    updated_at timestamp(0)
);

alter table public.sm_user_org
    owner to phillip;

create table if not exists public.sm_user_permission
(
    id            bigserial
        primary key,
    permission_id bigint not null
        constraint sm_user_permission_permission_id_foreign
            references public.sm_permission,
    user_id       bigint not null
        constraint sm_user_permission_user_id_foreign
            references public.sm_user,
    created_at    timestamp(0),
    updated_at    timestamp(0)
);

alter table public.sm_user_permission
    owner to phillip;

create table if not exists public.sm_user_role
(
    id         bigserial
        primary key,
    role_id    bigint not null
        constraint sm_user_role_role_id_foreign
            references public.sm_role,
    user_id    bigint not null
        constraint sm_user_role_user_id_foreign
            references public.sm_user,
    created_at timestamp(0),
    updated_at timestamp(0)
);

alter table public.sm_user_role
    owner to phillip;

create unique index if not exists sm_user_username_status_unique
    on public.sm_user (username)
    where ((status)::text = 'ACT'::text);

create table if not exists public.v_check_count
(
    count bigint
);

alter table public.v_check_count
    owner to phillip;

create table if not exists public.v_version
(
    max integer
);

alter table public.v_version
    owner to phillip;

create table if not exists public.ins_tv_claim_payment
(
    id              serial
        constraint ins_tv_claim_payment_no_pk
            primary key,
    status          varchar(3) default 'ACT'::character varying,
    created_at      timestamp  default now()                    not null,
    created_by      varchar                                     not null,
    updated_at      timestamp,
    updated_by      varchar,
    approved_status varchar(5) default 'PND'::character varying not null,
    approved_at     timestamp,
    approved_by     varchar,
    approved_cmt    text,
    claim_id        integer                                     not null,
    schema_id       integer                                     not null,
    payment_type    varchar,
    total_due       numeric                                     not null,
    total_paid      numeric                                     not null
);

alter table public.ins_tv_claim_payment
    owner to phillip;

create table if not exists public.ins_tv_claim_payment_detail
(
    id                     serial,
    status                 varchar(3) default 'ACT'::character varying,
    created_at             timestamp  default now() not null,
    created_by             varchar                  not null,
    updated_at             timestamp,
    updated_by             varchar,
    payment_id             integer                  not null,
    payment_no             varchar,
    claim_schema_detail_id integer                  not null,
    payee_id               integer                  not null,
    payment_method         varchar                  not null,
    paid_amount            numeric                  not null,
    remaining_balance      numeric                  not null,
    remark                 text
);

alter table public.ins_tv_claim_payment_detail
    owner to phillip;

create table if not exists public.v_claim_count
(
    count bigint
);

alter table public.v_claim_count
    owner to phillip;

