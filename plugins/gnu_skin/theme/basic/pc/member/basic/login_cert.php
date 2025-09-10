<?php
include_once '_common.php';

set_session("wv_cert_type",    get_session("ss_cert_type"));
set_session("wv_cert_no",      get_session("ss_cert_no"));
set_session("wv_cert_hash",    get_session("ss_cert_hash"));
set_session("wv_cert_adult",   get_session("ss_cert_adult"));
set_session("wv_cert_birth",   get_session("ss_cert_birth"));
set_session("wv_cert_sex",     get_session("ss_cert_sex"));
set_session('wv_cert_dupinfo', get_session("ss_cert_dupinfo"));
