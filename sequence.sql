create sequence public.action_events_id_seq;

alter sequence public.action_events_id_seq owner to phillip;

alter sequence public.action_events_id_seq owned by public.action_events.id;

create sequence public.data_id_seq;

alter sequence public.data_id_seq owner to phillip;

alter sequence public.data_id_seq owned by public.data.id;

create sequence public.failed_jobs_id_seq;

alter sequence public.failed_jobs_id_seq owner to phillip;

alter sequence public.failed_jobs_id_seq owned by public.failed_jobs.id;

create sequence public.fn_exchange_rate_id_seq
as integer;

alter sequence public.fn_exchange_rate_id_seq owner to phillip;

alter sequence public.fn_exchange_rate_id_seq owned by public.fn_exchange_rate.id;

create sequence public.fn_fin_cycle_id_seq
as integer;

alter sequence public.fn_fin_cycle_id_seq owner to phillip;

alter sequence public.fn_fin_cycle_id_seq owned by public.fn_fin_cycle.id;

create sequence public.fn_gl_master_id_seq
as integer;

alter sequence public.fn_gl_master_id_seq owner to phillip;

alter sequence public.fn_gl_master_id_seq owned by public.fn_gl_master.id;

create sequence public.fn_period_code_id_seq
as integer;

alter sequence public.fn_period_code_id_seq owner to phillip;

alter sequence public.fn_period_code_id_seq owned by public.fn_period_code.id;

create sequence public.fn_trn_event_id_seq
as integer;

alter sequence public.fn_trn_event_id_seq owner to phillip;

alter sequence public.fn_trn_event_id_seq owned by public.fn_trn_event.id;

create sequence public.fn_trn_event_template_id_seq
as integer;

alter sequence public.fn_trn_event_template_id_seq owner to phillip;

alter sequence public.fn_trn_event_template_id_seq owned by public.fn_trn_event_template.id;

create sequence public.fn_trn_log_id_seq
as integer;

alter sequence public.fn_trn_log_id_seq owner to phillip;

alter sequence public.fn_trn_log_id_seq owned by public.fn_trn_log.id;

create sequence public.idm_application_id_seq
as integer;

alter sequence public.idm_application_id_seq owner to phillip;

alter sequence public.idm_application_id_seq owned by public.idm_application.id;

create sequence public.idm_client_id_seq
as integer;

alter sequence public.idm_client_id_seq owner to phillip;

alter sequence public.idm_client_id_seq owned by public.idm_client.id;

create sequence public.idm_function_attr_id_seq
as integer;

alter sequence public.idm_function_attr_id_seq owner to phillip;

alter sequence public.idm_function_attr_id_seq owned by public.idm_function_attr.id;

create sequence public.idm_function_id_seq
as integer;

alter sequence public.idm_function_id_seq owner to phillip;

alter sequence public.idm_function_id_seq owned by public.idm_function.id;

create sequence public.idm_group_attr_id_seq
as integer;

alter sequence public.idm_group_attr_id_seq owner to phillip;

alter sequence public.idm_group_attr_id_seq owned by public.idm_group_attr.id;

create sequence public.idm_group_id_seq
as integer;

alter sequence public.idm_group_id_seq owner to phillip;

alter sequence public.idm_group_id_seq owned by public.idm_group.id;

create sequence public.idm_group_role_id_seq
as integer;

alter sequence public.idm_group_role_id_seq owner to phillip;

alter sequence public.idm_group_role_id_seq owned by public.idm_group_role.id;

create sequence public.idm_role_attr_id_seq
as integer;

alter sequence public.idm_role_attr_id_seq owner to phillip;

alter sequence public.idm_role_attr_id_seq owned by public.idm_role_attr.id;

create sequence public.idm_role_doa_authority_id_seq
as integer;

alter sequence public.idm_role_doa_authority_id_seq owner to phillip;

alter sequence public.idm_role_doa_authority_id_seq owned by public.idm_role_doa_authority.id;

create sequence public.idm_role_func_id_seq
as integer;

alter sequence public.idm_role_func_id_seq owner to phillip;

alter sequence public.idm_role_func_id_seq owned by public.idm_role_func.id;

create sequence public.idm_role_id_seq
as integer;

alter sequence public.idm_role_id_seq owner to phillip;

alter sequence public.idm_role_id_seq owned by public.idm_role.id;

create sequence public.idm_token_id_seq
as integer;

alter sequence public.idm_token_id_seq owner to phillip;

alter sequence public.idm_token_id_seq owned by public.idm_token.id;

create sequence public.idm_user_admin_pin_id_seq
as integer;

alter sequence public.idm_user_admin_pin_id_seq owner to phillip;

alter sequence public.idm_user_admin_pin_id_seq owned by public.idm_user_admin_pin.id;

create sequence public.idm_user_application_id_seq
as integer;

alter sequence public.idm_user_application_id_seq owner to phillip;

alter sequence public.idm_user_application_id_seq owned by public.idm_user_application.id;

create sequence public.idm_user_attr_id_seq
as integer;

alter sequence public.idm_user_attr_id_seq owner to phillip;

alter sequence public.idm_user_attr_id_seq owned by public.idm_user_attr.id;

create sequence public.idm_user_branch_id_seq
as integer;

alter sequence public.idm_user_branch_id_seq owner to phillip;

alter sequence public.idm_user_branch_id_seq owned by public.idm_user_branch.id;

create sequence public.idm_user_detail_id_seq
as integer;

alter sequence public.idm_user_detail_id_seq owner to phillip;

alter sequence public.idm_user_detail_id_seq owned by public.idm_user_detail.id;

create sequence public.idm_user_file_storage_id_seq
as integer;

alter sequence public.idm_user_file_storage_id_seq owner to phillip;

alter sequence public.idm_user_file_storage_id_seq owned by public.idm_user_file_storage.id;

create sequence public.idm_user_func_id_seq
as integer;

alter sequence public.idm_user_func_id_seq owner to phillip;

alter sequence public.idm_user_func_id_seq owned by public.idm_user_func.id;

create sequence public.idm_user_group_id_seq
as integer;

alter sequence public.idm_user_group_id_seq owner to phillip;

alter sequence public.idm_user_group_id_seq owned by public.idm_user_group.id;

create sequence public.idm_user_id_seq
as integer;

alter sequence public.idm_user_id_seq owner to phillip;

alter sequence public.idm_user_id_seq owned by public.idm_user.id;

create sequence public.idm_user_log_id_seq
as integer;

alter sequence public.idm_user_log_id_seq owner to phillip;

alter sequence public.idm_user_log_id_seq owned by public.idm_user_log.id;

create sequence public.idm_user_role_id_seq
as integer;

alter sequence public.idm_user_role_id_seq owner to phillip;

alter sequence public.idm_user_role_id_seq owned by public.idm_user_role.id;

create sequence public.idm_validity_id_seq
as integer;

alter sequence public.idm_validity_id_seq owner to phillip;

alter sequence public.idm_validity_id_seq owned by public.idm_validity.id;

create sequence public.ins_account_personnel_id_seq
as integer;

alter sequence public.ins_account_personnel_id_seq owner to phillip;

alter sequence public.ins_account_personnel_id_seq owned by public.ins_account_personnel.id;

create sequence public.ins_address_code_id_seq
as integer;

alter sequence public.ins_address_code_id_seq owner to phillip;

alter sequence public.ins_address_code_id_seq owned by public.ins_address_code.id;

create sequence public.ins_auto_data_clause_id_seq
as integer;

alter sequence public.ins_auto_data_clause_id_seq owner to phillip;

alter sequence public.ins_auto_data_clause_id_seq owned by public.ins_auto_data_clause.id;

create sequence public.ins_auto_data_detail_id_seq
as integer;

alter sequence public.ins_auto_data_detail_id_seq owner to phillip;

alter sequence public.ins_auto_data_detail_id_seq owned by public.ins_auto_data_detail.id;

create sequence public.ins_auto_data_detail_temp_id_seq
as integer;

alter sequence public.ins_auto_data_detail_temp_id_seq owner to phillip;

alter sequence public.ins_auto_data_detail_temp_id_seq owned by public.ins_auto_data_detail_temp.id;

create sequence public.ins_auto_data_master_id_seq
as integer;

alter sequence public.ins_auto_data_master_id_seq owner to phillip;

alter sequence public.ins_auto_data_master_id_seq owned by public.ins_auto_data_master.id;

create sequence public.ins_branch_id_seq
as integer;

alter sequence public.ins_branch_id_seq owner to phillip;

alter sequence public.ins_branch_id_seq owned by public.ins_branch.id;

create sequence public.ins_business_category_id_seq
as integer;

alter sequence public.ins_business_category_id_seq owner to phillip;

alter sequence public.ins_business_category_id_seq owned by public.ins_business_category.id;

create sequence public.ins_business_channel_id_seq
as integer;

alter sequence public.ins_business_channel_id_seq owner to phillip;

alter sequence public.ins_business_channel_id_seq owned by public.ins_business_channel.id;

create sequence public.ins_business_handler_id_seq
as integer;

alter sequence public.ins_business_handler_id_seq owner to phillip;

alter sequence public.ins_business_handler_id_seq owned by public.ins_business_handler.id;

create sequence public.ins_claim_adjuster_company_id_seq
as integer;

alter sequence public.ins_claim_adjuster_company_id_seq owner to phillip;

alter sequence public.ins_claim_adjuster_company_id_seq owned by public.ins_claim_adjuster_company.id;

create sequence public.ins_claim_cause_of_loss_id_seq
as integer;

alter sequence public.ins_claim_cause_of_loss_id_seq owner to phillip;

alter sequence public.ins_claim_cause_of_loss_id_seq owned by public.ins_claim_cause_of_loss.id;

create sequence public.ins_claim_component_id_seq
as integer;

alter sequence public.ins_claim_component_id_seq owner to phillip;

alter sequence public.ins_claim_component_id_seq owned by public.ins_claim_detail.id;

create sequence public.ins_claim_document_id_seq
as integer;

alter sequence public.ins_claim_document_id_seq owner to phillip;

alter sequence public.ins_claim_document_id_seq owned by public.ins_claim_document.id;

create sequence public.ins_claim_generate_payment_or_claim_no_id_seq
as integer;

alter sequence public.ins_claim_generate_payment_or_claim_no_id_seq owner to phillip;

alter sequence public.ins_claim_generate_payment_or_claim_no_id_seq owned by public.ins_claim_generate_payment_or_claim_no.id;

create sequence public.ins_claim_id_seq
as integer;

alter sequence public.ins_claim_id_seq owner to phillip;

alter sequence public.ins_claim_id_seq owned by public.ins_claim.id;

create sequence public.ins_claim_occupation_group_id_seq
as integer;

alter sequence public.ins_claim_occupation_group_id_seq owner to phillip;

alter sequence public.ins_claim_occupation_group_id_seq owned by public.ins_claim_service_provider.id;

create sequence public.ins_claim_payee_id_seq
as integer;

alter sequence public.ins_claim_payee_id_seq owner to phillip;

alter sequence public.ins_claim_payee_id_seq owned by public.ins_claim_payee.id;

create sequence public.ins_claim_payment_detail_id_seq
as integer;

alter sequence public.ins_claim_payment_detail_id_seq owner to phillip;

alter sequence public.ins_claim_payment_detail_id_seq owned by public.ins_claim_payment_detail.id;

create sequence public.ins_claim_payment_id_seq
as integer;

alter sequence public.ins_claim_payment_id_seq owner to phillip;

alter sequence public.ins_claim_payment_id_seq owned by public.ins_claim_payment.id;

create sequence public.ins_claim_third_party_id_seq
as integer;

alter sequence public.ins_claim_third_party_id_seq owner to phillip;

alter sequence public.ins_claim_third_party_id_seq owned by public.ins_claim_third_party.id;

create sequence public.ins_claim_transaction_detail_id_seq
as integer;

alter sequence public.ins_claim_transaction_detail_id_seq owner to phillip;

alter sequence public.ins_claim_transaction_detail_id_seq owned by public.ins_claim_transaction_detail.id;

create sequence public.ins_claim_transaction_detail_temp_id_seq
as integer;

alter sequence public.ins_claim_transaction_detail_temp_id_seq owner to phillip;

alter sequence public.ins_claim_transaction_detail_temp_id_seq owned by public.ins_claim_transaction_detail_temp.id;

create sequence public.ins_claim_transaction_id_seq
as integer;

alter sequence public.ins_claim_transaction_id_seq owner to phillip;

alter sequence public.ins_claim_transaction_id_seq owned by public.ins_claim_transaction.id;

create sequence public.ins_config_surcharge_id_seq
as integer;

alter sequence public.ins_config_surcharge_id_seq owner to phillip;

alter sequence public.ins_config_surcharge_id_seq owned by public.ins_config_surcharge.id;

create sequence public.ins_country_id_seq
as integer;

alter sequence public.ins_country_id_seq owner to phillip;

alter sequence public.ins_country_id_seq owned by public.ins_country.id;

create sequence public.ins_cust_contact_id_seq
as integer;

alter sequence public.ins_cust_contact_id_seq owner to phillip;

alter sequence public.ins_cust_contact_id_seq owned by public.ins_cust_contact.id;

create sequence public.ins_cust_corporate_id_seq
as integer;

alter sequence public.ins_cust_corporate_id_seq owner to phillip;

alter sequence public.ins_cust_corporate_id_seq owned by public.ins_cust_corporate.id;

create sequence public.ins_cust_individual_id_seq
as integer;

alter sequence public.ins_cust_individual_id_seq owner to phillip;

alter sequence public.ins_cust_individual_id_seq owned by public.ins_cust_individual.id;

create sequence public.ins_cust_point_of_contact_id_seq
as integer;

alter sequence public.ins_cust_point_of_contact_id_seq owner to phillip;

alter sequence public.ins_cust_point_of_contact_id_seq owned by public.ins_cust_point_of_contact.id;

create sequence public.ins_customer_id_seq
as integer;

alter sequence public.ins_customer_id_seq owner to phillip;

alter sequence public.ins_customer_id_seq owned by public.ins_customer.id;

create sequence public.ins_driver_license_id_seq
as integer;

alter sequence public.ins_driver_license_id_seq owner to phillip;

alter sequence public.ins_driver_license_id_seq owned by public.ins_claim_driver_license.id;

create sequence public.ins_generate_sequences_no
maxvalue 9999999
cache 10;

alter sequence public.ins_generate_sequences_no owner to phillip;

create sequence public.ins_hs_age_setup_id_seq
as integer;

alter sequence public.ins_hs_age_setup_id_seq owner to phillip;

alter sequence public.ins_hs_age_setup_id_seq owned by public.ins_hs_age_setup.id;

create sequence public.ins_hs_data_master_detail_id_seq
as integer;

alter sequence public.ins_hs_data_master_detail_id_seq owner to phillip;

alter sequence public.ins_hs_data_master_detail_id_seq owned by public.ins_hs_data_detail.id;

create sequence public.ins_hs_data_master_v1_id_seq
as integer;

alter sequence public.ins_hs_data_master_v1_id_seq owner to phillip;

alter sequence public.ins_hs_data_master_v1_id_seq owned by public.ins_hs_data_master.id;

create sequence public.ins_hs_plan_data_detail_id_seq
as integer;

alter sequence public.ins_hs_plan_data_detail_id_seq owner to phillip;

alter sequence public.ins_hs_plan_data_detail_id_seq owned by public.ins_hs_plan_data_detail.id;

create sequence public.ins_hs_plan_data_id_seq
as integer;

alter sequence public.ins_hs_plan_data_id_seq owner to phillip;

alter sequence public.ins_hs_plan_data_id_seq owned by public.ins_hs_plan_data.id;

create sequence public.ins_hs_schema_detail_id_seq
as integer;

alter sequence public.ins_hs_schema_detail_id_seq owner to phillip;

alter sequence public.ins_hs_schema_detail_id_seq owned by public.ins_hs_schema_detail.id;

create sequence public.ins_hs_schema_id_seq
as integer;

alter sequence public.ins_hs_schema_id_seq owner to phillip;

alter sequence public.ins_hs_schema_id_seq owned by public.ins_hs_schema.id;

create sequence public.ins_hs_standard_data_id_seq
as integer;

alter sequence public.ins_hs_standard_data_id_seq owner to phillip;

alter sequence public.ins_hs_standard_data_id_seq owned by public.ins_hs_schema_data.id;

create sequence public.ins_insurance_clause_id_seq
as integer;

alter sequence public.ins_insurance_clause_id_seq owner to phillip;

alter sequence public.ins_insurance_clause_id_seq owned by public.ins_insurance_clause.id;

create sequence public.ins_insurance_ncd_id_seq
as integer;

alter sequence public.ins_insurance_ncd_id_seq owner to phillip;

alter sequence public.ins_insurance_ncd_id_seq owned by public.ins_insurance_ncd.id;

create sequence public.ins_joint_account_detail_id_seq
as integer;

alter sequence public.ins_joint_account_detail_id_seq owner to phillip;

alter sequence public.ins_joint_account_detail_id_seq owned by public.ins_joint_account_detail.id;

create sequence public.ins_lov_vehicle_classification_id_seq
as integer;

alter sequence public.ins_lov_vehicle_classification_id_seq owner to phillip;

alter sequence public.ins_lov_vehicle_classification_id_seq owned by public.ins_lov_vehicle_classification.id;

create sequence public.ins_lov_vehicle_make_id_seq
as integer;

alter sequence public.ins_lov_vehicle_make_id_seq owner to phillip;

alter sequence public.ins_lov_vehicle_make_id_seq owned by public.ins_lov_vehicle_make.id;

create sequence public.ins_lov_vehicle_model_id_seq
as integer;

alter sequence public.ins_lov_vehicle_model_id_seq owner to phillip;

alter sequence public.ins_lov_vehicle_model_id_seq owned by public.ins_lov_vehicle_model.id;

create sequence public.ins_lov_vehicle_rules_id_seq
as integer;

alter sequence public.ins_lov_vehicle_rules_id_seq owner to phillip;

alter sequence public.ins_lov_vehicle_rules_id_seq owned by public.ins_lov_vehicle_rules.id;

create sequence public.ins_lov_vehicle_usage_id_seq
as integer;

alter sequence public.ins_lov_vehicle_usage_id_seq owner to phillip;

alter sequence public.ins_lov_vehicle_usage_id_seq owned by public.ins_lov_vehicle_usage.id;

create sequence public.ins_mode_of_payment_id_seq
as integer;

alter sequence public.ins_mode_of_payment_id_seq owner to phillip;

alter sequence public.ins_mode_of_payment_id_seq owned by public.ins_mode_of_payment.id;

create sequence public.ins_partner_insurance_company_id_seq
as integer;

alter sequence public.ins_partner_insurance_company_id_seq owner to phillip;

alter sequence public.ins_partner_insurance_company_id_seq owned by public.ins_partner_insurance_company.id;

create sequence public.ins_policy_commission_data_id_seq
as integer;

alter sequence public.ins_policy_commission_data_id_seq owner to phillip;

alter sequence public.ins_policy_commission_data_id_seq owned by public.ins_policy_commission_data.id;

create sequence public.ins_policy_customer_id_seq
as integer;

alter sequence public.ins_policy_customer_id_seq owner to phillip;

alter sequence public.ins_policy_customer_id_seq owned by public.ins_policy_customer.id;

create sequence public.ins_policy_endor_commission_hist_id_seq
as integer;

alter sequence public.ins_policy_endor_commission_hist_id_seq owner to phillip;

alter sequence public.ins_policy_endor_commission_hist_id_seq owned by public.ins_policy_endor_commission_hist.id;

create sequence public.ins_policy_id_seq
as integer;

alter sequence public.ins_policy_id_seq owner to phillip;

alter sequence public.ins_policy_id_seq owned by public.ins_policy.id;

create sequence public.ins_policy_invoice_note_id_seq
as integer;

alter sequence public.ins_policy_invoice_note_id_seq owner to phillip;

alter sequence public.ins_policy_invoice_note_id_seq owned by public.ins_policy_invoice_note.id;

create sequence public.ins_policy_trn_log_id_seq
as integer;

alter sequence public.ins_policy_trn_log_id_seq owner to phillip;

alter sequence public.ins_policy_trn_log_id_seq owned by public.ins_policy_trn_log.id;

create sequence public.ins_policy_wording_version_id_seq
as integer;

alter sequence public.ins_policy_wording_version_id_seq owner to phillip;

alter sequence public.ins_policy_wording_version_id_seq owned by public.ins_policy_wording_version.id;

create sequence public.ins_prod_auto_vehicle_cubic_id_seq
as integer;

alter sequence public.ins_prod_auto_vehicle_cubic_id_seq owner to phillip;

alter sequence public.ins_prod_auto_vehicle_cubic_id_seq owned by public.ins_prod_auto_vehicle_cubic.id;

create sequence public.ins_prod_auto_vehicle_maker_id_seq
as integer;

alter sequence public.ins_prod_auto_vehicle_maker_id_seq owner to phillip;

alter sequence public.ins_prod_auto_vehicle_maker_id_seq owned by public.ins_prod_auto_vehicle_maker.id;

create sequence public.ins_prod_auto_vehicle_model_id_seq
as integer;

alter sequence public.ins_prod_auto_vehicle_model_id_seq owner to phillip;

alter sequence public.ins_prod_auto_vehicle_model_id_seq owned by public.ins_prod_auto_vehicle_model.id;

create sequence public.ins_prod_comp_data_id_seq
as integer;

alter sequence public.ins_prod_comp_data_id_seq owner to phillip;

alter sequence public.ins_prod_comp_data_id_seq owned by public.ins_prod_comp_data.id;

create sequence public.ins_prod_comp_formula_id_seq
as integer;

alter sequence public.ins_prod_comp_formula_id_seq owner to phillip;

alter sequence public.ins_prod_comp_formula_id_seq owned by public.ins_prod_comp_formula.id;

create sequence public.ins_prod_comp_frm_elem_id_seq
as integer;

alter sequence public.ins_prod_comp_frm_elem_id_seq owner to phillip;

alter sequence public.ins_prod_comp_frm_elem_id_seq owned by public.ins_prod_comp_frm_elem.id;

create sequence public.ins_prod_comp_frm_expr_id_seq
as integer;

alter sequence public.ins_prod_comp_frm_expr_id_seq owner to phillip;

alter sequence public.ins_prod_comp_frm_expr_id_seq owned by public.ins_prod_comp_frm_expr.id;

create sequence public.ins_prod_component_id_seq
as integer;

alter sequence public.ins_prod_component_id_seq owner to phillip;

alter sequence public.ins_prod_component_id_seq owned by public.ins_prod_component.id;

create sequence public.ins_prod_cond_rating_id_seq
as integer;

alter sequence public.ins_prod_cond_rating_id_seq owner to phillip;

alter sequence public.ins_prod_cond_rating_id_seq owned by public.ins_prod_cond_rating.id;

create sequence public.ins_prod_cover_package_id_seq
as integer;

alter sequence public.ins_prod_cover_package_id_seq owner to phillip;

alter sequence public.ins_prod_cover_package_id_seq owned by public.ins_prod_cover_package.id;

create sequence public.ins_prod_cpkg_comp_id_seq
as integer;

alter sequence public.ins_prod_cpkg_comp_id_seq owner to phillip;

alter sequence public.ins_prod_cpkg_comp_id_seq owned by public.ins_prod_cpkg_comp.id;

create sequence public.ins_prod_deductible_detail_id_seq
as integer;

alter sequence public.ins_prod_deductible_detail_id_seq owner to phillip;

alter sequence public.ins_prod_deductible_detail_id_seq owned by public.ins_prod_deductible_detail.id;

create sequence public.ins_prod_deductible_id_seq
as integer;

alter sequence public.ins_prod_deductible_id_seq owner to phillip;

alter sequence public.ins_prod_deductible_id_seq owned by public.ins_prod_deductible.id;

create sequence public.ins_product_id_seq
as integer;

alter sequence public.ins_product_id_seq owner to phillip;

alter sequence public.ins_product_id_seq owned by public.ins_product.id;

create sequence public.ins_product_line_id_seq
as integer;

alter sequence public.ins_product_line_id_seq owner to phillip;

alter sequence public.ins_product_line_id_seq owned by public.ins_product_line.id;

create sequence public.ins_quotation_id_seq
as integer;

alter sequence public.ins_quotation_id_seq owner to phillip;

alter sequence public.ins_quotation_id_seq owned by public.ins_quotation.id;

create sequence public.ins_ref_enum_id_seq
as integer;

alter sequence public.ins_ref_enum_id_seq owner to phillip;

alter sequence public.ins_ref_enum_id_seq owned by public.ins_ref_enum.id;

create sequence public.ins_reinsurance_config_id_seq
as integer;

alter sequence public.ins_reinsurance_config_id_seq owner to phillip;

alter sequence public.ins_reinsurance_config_id_seq owned by public.ins_reinsurance_config.id;

create sequence public.ins_reinsurance_data_id_seq
as integer;

alter sequence public.ins_reinsurance_data_id_seq owner to phillip;

alter sequence public.ins_reinsurance_data_id_seq owned by public.ins_reinsurance_data.id;

create sequence public.ins_reinsurance_id_seq
as integer;

alter sequence public.ins_reinsurance_id_seq owner to phillip;

alter sequence public.ins_reinsurance_id_seq owned by public.ins_reinsurance.id;

create sequence public.ins_reinsurance_partner_group_id_seq
as integer;

alter sequence public.ins_reinsurance_partner_group_id_seq owner to phillip;

alter sequence public.ins_reinsurance_partner_group_id_seq owned by public.ins_reinsurance_partner_group.id;

create sequence public.ins_reinsurance_partner_id_seq
as integer;

alter sequence public.ins_reinsurance_partner_id_seq owner to phillip;

alter sequence public.ins_reinsurance_partner_id_seq owned by public.ins_reinsurance_partner.id;

create sequence public.ins_reinsurance_type_id_seq
as integer;

alter sequence public.ins_reinsurance_type_id_seq owner to phillip;

alter sequence public.ins_reinsurance_type_id_seq owned by public.ins_reinsurance_type.id;

create sequence public.ins_renewal_policy_id_seq
as integer;

alter sequence public.ins_renewal_policy_id_seq owner to phillip;

alter sequence public.ins_renewal_policy_id_seq owned by public.ins_renewal_policy.id;

create sequence public.ins_treaty_config_id_seq
as integer;

alter sequence public.ins_treaty_config_id_seq owner to phillip;

alter sequence public.ins_treaty_config_id_seq owned by public.ins_treaty_config.id;

create sequence public.ins_vehicle_id_seq
as integer;

alter sequence public.ins_vehicle_id_seq owner to phillip;

alter sequence public.ins_vehicle_id_seq owned by public.ins_vehicle.id;

create sequence public.ins_vehicle_model_id_seq
as integer;

alter sequence public.ins_vehicle_model_id_seq owner to phillip;

alter sequence public.ins_vehicle_model_id_seq owned by public.ins_vehicle_model.id;

create sequence public.mb_customer_config_id_seq
as integer;

alter sequence public.mb_customer_config_id_seq owner to phillip;

alter sequence public.mb_customer_config_id_seq owned by public.mi_customer_config.id;

create sequence public.mb_customer_history_id_seq
as integer;

alter sequence public.mb_customer_history_id_seq owner to phillip;

alter sequence public.mb_customer_history_id_seq owned by public.mi_customer_history.id;

create sequence public.mb_customer_id_seq
as integer;

alter sequence public.mb_customer_id_seq owner to phillip;

alter sequence public.mb_customer_id_seq owned by public.mb_customer.id;

create sequence public.mb_customer_session_id_seq
as integer;

alter sequence public.mb_customer_session_id_seq owner to phillip;

alter sequence public.mb_customer_session_id_seq owned by public.mi_customer_session.id;

create sequence public.mb_device_id_seq
as integer;

alter sequence public.mb_device_id_seq owner to phillip;

alter sequence public.mb_device_id_seq owned by public.mi_device.id;

create sequence public.mb_error_code_id_seq
as integer;

alter sequence public.mb_error_code_id_seq owner to phillip;

alter sequence public.mb_error_code_id_seq owned by public.mi_error_code.id;

create sequence public.mb_module_id_seq
as integer;

alter sequence public.mb_module_id_seq owner to phillip;

alter sequence public.mb_module_id_seq owned by public.mi_module.id;

create sequence public.mb_request_log_id_seq
as integer;

alter sequence public.mb_request_log_id_seq owner to phillip;

alter sequence public.mb_request_log_id_seq owned by public.mi_request_log.id;

create sequence public.mi_reponse_code_id_seq
as integer;

alter sequence public.mi_reponse_code_id_seq owner to phillip;

alter sequence public.mi_reponse_code_id_seq owned by public.mi_response_code.id;

create sequence public.migrations_id_seq
as integer;

alter sequence public.migrations_id_seq owner to phillip;

alter sequence public.migrations_id_seq owned by public.migrations.id;

create sequence public.oauth_clients_id_seq;

alter sequence public.oauth_clients_id_seq owner to phillip;

alter sequence public.oauth_clients_id_seq owned by public.oauth_clients.id;

create sequence public.oauth_personal_access_clients_id_seq;

alter sequence public.oauth_personal_access_clients_id_seq owner to phillip;

alter sequence public.oauth_personal_access_clients_id_seq owned by public.oauth_personal_access_clients.id;

create sequence public.personal_access_tokens_id_seq;

alter sequence public.personal_access_tokens_id_seq owner to phillip;

alter sequence public.personal_access_tokens_id_seq owned by public.personal_access_tokens.id;

create sequence public.tmp_data_id_seq
as integer;

alter sequence public.tmp_data_id_seq owner to phillip;

alter sequence public.tmp_data_id_seq owned by public.tmp_data.id;

create sequence public.tmp_ins_config_id_seq
as integer;

alter sequence public.tmp_ins_config_id_seq owner to phillip;

alter sequence public.tmp_ins_config_id_seq owned by public.tmp_ins_config.id;

create sequence public.tmp_ins_customer_attr_id_seq
as integer;

alter sequence public.tmp_ins_customer_attr_id_seq owner to phillip;

alter sequence public.tmp_ins_customer_attr_id_seq owned by public.tmp_ins_customer_attr.id;

create sequence public.tmp_ins_customer_id_seq
as integer;

alter sequence public.tmp_ins_customer_id_seq owner to phillip;

alter sequence public.tmp_ins_customer_id_seq owned by public.tmp_ins_customer.id;

create sequence public.tmp_ins_policy_id_seq
as integer;

alter sequence public.tmp_ins_policy_id_seq owner to phillip;

alter sequence public.tmp_ins_policy_id_seq owned by public.tmp_ins_policy.id;

create sequence public.tmp_ins_policy_record_log_id_seq
as integer;

alter sequence public.tmp_ins_policy_record_log_id_seq owner to phillip;

alter sequence public.tmp_ins_policy_record_log_id_seq owned by public.tmp_ins_policy_record_log.id;

create sequence public.users_id_seq;

alter sequence public.users_id_seq owner to phillip;

alter sequence public.users_id_seq owned by public.users.id;

create sequence public.ins_hs_quotation_id_seq
as integer;

alter sequence public.ins_hs_quotation_id_seq owner to phillip;

alter sequence public.ins_hs_quotation_id_seq owned by public.ins_hs_quotation.id;

create sequence public.ins_hs_data_clause_id_seq
as integer;

alter sequence public.ins_hs_data_clause_id_seq owner to phillip;

alter sequence public.ins_hs_data_clause_id_seq owned by public.ins_hs_data_clause.id;

create sequence public.ins_hs_policy_id_seq
as integer;

alter sequence public.ins_hs_policy_id_seq owner to phillip;

alter sequence public.ins_hs_policy_id_seq owned by public.ins_hs_policy.id;

create sequence public.ins_hs_policy_invoice_note_id_seq
as integer;

alter sequence public.ins_hs_policy_invoice_note_id_seq owner to phillip;

alter sequence public.ins_hs_policy_invoice_note_id_seq owned by public.ins_hs_policy_invoice_note.id;

create sequence public.ins_hs_policy_commission_data_id_seq
as integer;

alter sequence public.ins_hs_policy_commission_data_id_seq owner to phillip;

alter sequence public.ins_hs_policy_commission_data_id_seq owned by public.ins_hs_policy_commission_data.id;

create sequence public.ins_hs_translation_id_seq
as integer;

alter sequence public.ins_hs_translation_id_seq owner to phillip;

alter sequence public.ins_hs_translation_id_seq owned by public.ins_hs_translation.id;

create sequence public.sys_enum_id_seq
as integer;

alter sequence public.sys_enum_id_seq owner to phillip;

alter sequence public.sys_enum_id_seq owned by public.sys_enum.id;

create sequence public.ins_hs_reinsurance_config_id_seq
as integer;

alter sequence public.ins_hs_reinsurance_config_id_seq owner to phillip;

alter sequence public.ins_hs_reinsurance_config_id_seq owned by public.ins_hs_reinsurance_config.id;

create sequence public.ins_hs_reinsurance_data_id_seq
as integer;

alter sequence public.ins_hs_reinsurance_data_id_seq owner to phillip;

alter sequence public.ins_hs_reinsurance_data_id_seq owned by public.ins_hs_reinsurance_data.id;

create sequence public.ins_hs_schema_data_sequences_id_seq
as integer;

alter sequence public.ins_hs_schema_data_sequences_id_seq owner to phillip;

alter sequence public.ins_hs_schema_data_sequences_id_seq owned by public.ins_hs_schema_data_sequences.id;

create sequence public.ins_hs_clinic_id_seq
as integer;

alter sequence public.ins_hs_clinic_id_seq owner to phillip;

alter sequence public.ins_hs_clinic_id_seq owned by public.ins_hs_clinic.id;

create sequence public.ins_hs_claim_id_seq
as integer;

alter sequence public.ins_hs_claim_id_seq owner to phillip;

alter sequence public.ins_hs_claim_id_seq owned by public.ins_hs_claim.id;

create sequence public.ins_hs_claim_generate_payment_or_claim_no_id_seq
as integer;

alter sequence public.ins_hs_claim_generate_payment_or_claim_no_id_seq owner to phillip;

alter sequence public.ins_hs_claim_generate_payment_or_claim_no_id_seq owned by public.ins_hs_claim_generate_payment_or_claim_no.id;

create sequence public.ins_hs_claim_detail_id_seq
as integer;

alter sequence public.ins_hs_claim_detail_id_seq owner to phillip;

alter sequence public.ins_hs_claim_detail_id_seq owned by public.ins_hs_claim_detail.id;

create sequence public.ins_hs_claim_schema_data_id_seq
as integer;

alter sequence public.ins_hs_claim_schema_data_id_seq owner to phillip;

alter sequence public.ins_hs_claim_schema_data_id_seq owned by public.ins_hs_claim_schema_data.id;

create sequence public.ins_hs_claim_transaction_id_seq
as integer;

alter sequence public.ins_hs_claim_transaction_id_seq owner to phillip;

alter sequence public.ins_hs_claim_transaction_id_seq owned by public.ins_hs_claim_transaction.id;

create sequence public.sm_app_id_seq;

alter sequence public.sm_app_id_seq owner to phillip;

alter sequence public.sm_app_id_seq owned by public.sm_app.id;

create sequence public.sm_group_id_seq;

alter sequence public.sm_group_id_seq owner to phillip;

alter sequence public.sm_group_id_seq owned by public.sm_group.id;

create sequence public.sm_org_id_seq;

alter sequence public.sm_org_id_seq owner to phillip;

alter sequence public.sm_org_id_seq owned by public.sm_org.id;

create sequence public.sm_role_id_seq;

alter sequence public.sm_role_id_seq owner to phillip;

alter sequence public.sm_role_id_seq owned by public.sm_role.id;

create sequence public.sm_branch_id_seq;

alter sequence public.sm_branch_id_seq owner to phillip;

alter sequence public.sm_branch_id_seq owned by public.sm_branch.id;

create sequence public.sm_default_id_seq;

alter sequence public.sm_default_id_seq owner to phillip;

alter sequence public.sm_default_id_seq owned by public.sm_default.id;

create sequence public.sm_function_id_seq;

alter sequence public.sm_function_id_seq owner to phillip;

alter sequence public.sm_function_id_seq owned by public.sm_function.id;

create sequence public.sm_group_role_id_seq;

alter sequence public.sm_group_role_id_seq owner to phillip;

alter sequence public.sm_group_role_id_seq owned by public.sm_group_role.id;

create sequence public.sm_permission_id_seq;

alter sequence public.sm_permission_id_seq owner to phillip;

alter sequence public.sm_permission_id_seq owned by public.sm_permission.id;

create sequence public.sm_permission_role_id_seq;

alter sequence public.sm_permission_role_id_seq owner to phillip;

alter sequence public.sm_permission_role_id_seq owned by public.sm_permission_role.id;

create sequence public.sm_token_id_seq;

alter sequence public.sm_token_id_seq owner to phillip;

alter sequence public.sm_token_id_seq owned by public.sm_token.id;

create sequence public.sm_user_branch_id_seq;

alter sequence public.sm_user_branch_id_seq owner to phillip;

alter sequence public.sm_user_branch_id_seq owned by public.sm_user_branch.id;

create sequence public.sm_user_group_id_seq;

alter sequence public.sm_user_group_id_seq owner to phillip;

alter sequence public.sm_user_group_id_seq owned by public.sm_user_group.id;

create sequence public.sm_user_org_id_seq;

alter sequence public.sm_user_org_id_seq owner to phillip;

alter sequence public.sm_user_org_id_seq owned by public.sm_user_org.id;

create sequence public.sm_user_permission_id_seq;

alter sequence public.sm_user_permission_id_seq owner to phillip;

alter sequence public.sm_user_permission_id_seq owned by public.sm_user_permission.id;

create sequence public.sm_user_role_id_seq;

alter sequence public.sm_user_role_id_seq owner to phillip;

alter sequence public.sm_user_role_id_seq owned by public.sm_user_role.id;

create sequence public.wfl_template_predef_keyword_id_seq
as integer;

alter sequence public.wfl_template_predef_keyword_id_seq owner to phillip;

alter sequence public.wfl_template_predef_keyword_id_seq owned by public.wfl_template_predef_keyword.id;

create sequence public.sys_audit_log_id_seq;

alter sequence public.sys_audit_log_id_seq owner to phillip;

alter sequence public.sys_audit_log_id_seq owned by public.sys_audit_log.id;

create sequence public.my_table_id_seq
as integer;

alter sequence public.my_table_id_seq owner to phillip;

alter sequence public.my_table_id_seq owned by public.my_table.id;

create sequence public.ois_external_task_log_id_seq
as integer;

alter sequence public.ois_external_task_log_id_seq owner to phillip;

alter sequence public.ois_external_task_log_id_seq owned by public.ois_external_task_log.id;

create sequence public.ins_pa_working_class_id_seq
as integer;

alter sequence public.ins_pa_working_class_id_seq owner to phillip;

alter sequence public.ins_pa_working_class_id_seq owned by public.ins_pa_working_class.id;

create sequence public.ins_pa_data_detail_id_seq
as integer;

alter sequence public.ins_pa_data_detail_id_seq owner to phillip;

alter sequence public.ins_pa_data_detail_id_seq owned by public.ins_pa_data_detail.id;

create sequence public.ins_pa_data_master_id_seq
as integer;

alter sequence public.ins_pa_data_master_id_seq owner to phillip;

alter sequence public.ins_pa_data_master_id_seq owned by public.ins_pa_data_master.id;

create sequence public.ins_pa_insured_person_calc_final_id_seq
as integer;

alter sequence public.ins_pa_insured_person_calc_final_id_seq owner to phillip;

alter sequence public.ins_pa_insured_person_calc_final_id_seq owned by public.ins_pa_insured_person_calc_final.id;

create sequence public.ins_pa_quotation_id_seq
as integer;

alter sequence public.ins_pa_quotation_id_seq owner to phillip;

alter sequence public.ins_pa_quotation_id_seq owned by public.ins_pa_quotation.id;

create sequence public.ins_pa_policy_id_seq
as integer;

alter sequence public.ins_pa_policy_id_seq owner to phillip;

alter sequence public.ins_pa_policy_id_seq owned by public.ins_pa_policy.id;

create sequence public.ins_pa_insured_person_calc_id_seq
as integer;

alter sequence public.ins_pa_insured_person_calc_id_seq owner to phillip;

alter sequence public.ins_pa_insured_person_calc_id_seq owned by public.ins_pa_insured_person_calc.id;

create sequence public.ins_pa_policy_commission_data_id_seq
as integer;

alter sequence public.ins_pa_policy_commission_data_id_seq owner to phillip;

alter sequence public.ins_pa_policy_commission_data_id_seq owned by public.ins_pa_policy_commission_data.id;

create sequence public.ins_pa_reinsurance_config_id_seq
as integer;

alter sequence public.ins_pa_reinsurance_config_id_seq owner to phillip;

alter sequence public.ins_pa_reinsurance_config_id_seq owned by public.ins_pa_reinsurance_config.id;

create sequence public.ins_pa_reinsurance_data_id_seq
as integer;

alter sequence public.ins_pa_reinsurance_data_id_seq owner to phillip;

alter sequence public.ins_pa_reinsurance_data_id_seq owned by public.ins_pa_reinsurance_data.id;

create sequence public.ins_hs_policy_endor_commission_hist_id_seq
as integer;

alter sequence public.ins_hs_policy_endor_commission_hist_id_seq owner to phillip;

alter sequence public.ins_hs_policy_endor_commission_hist_id_seq owned by public.ins_hs_policy_endor_commission_hist.id;

create sequence public.ins_pa_policy_endor_commission_hist_id_seq
as integer;

alter sequence public.ins_pa_policy_endor_commission_hist_id_seq owner to phillip;

alter sequence public.ins_pa_policy_endor_commission_hist_id_seq owned by public.ins_pa_policy_endor_commission_hist.id;

create sequence public.ins_pa_policy_invoice_note_id_seq
as integer;

alter sequence public.ins_pa_policy_invoice_note_id_seq owner to phillip;

alter sequence public.ins_pa_policy_invoice_note_id_seq owned by public.ins_pa_policy_invoice_note.id;

create sequence public.ins_tv_zone_id_seq
as integer;

alter sequence public.ins_tv_zone_id_seq owner to phillip;

alter sequence public.ins_tv_zone_id_seq owned by public.ins_tv_zone.id;

create sequence public.ins_tv_plan_id_seq
as integer;

alter sequence public.ins_tv_plan_id_seq owner to phillip;

alter sequence public.ins_tv_plan_id_seq owned by public.ins_tv_plan.id;

create sequence public.ins_tv_duration_range_id_seq
as integer;

alter sequence public.ins_tv_duration_range_id_seq owner to phillip;

alter sequence public.ins_tv_duration_range_id_seq owned by public.ins_tv_coverage_duration.id;

create sequence public.ins_tv_duration_plan_zone_package_id_seq
as integer;

alter sequence public.ins_tv_duration_plan_zone_package_id_seq owner to phillip;

alter sequence public.ins_tv_duration_plan_zone_package_id_seq owned by public.test_duration_plan_zone_package.id;

create sequence public.test_ins_tv_premium_rate_id_seq
as integer;

alter sequence public.test_ins_tv_premium_rate_id_seq owner to phillip;

alter sequence public.test_ins_tv_premium_rate_id_seq owned by public.test_ins_tv_premium_rate.id;

create sequence public.ins_tv_premium_rate_id_seq
as integer;

alter sequence public.ins_tv_premium_rate_id_seq owner to phillip;

alter sequence public.ins_tv_premium_rate_id_seq owned by public.ins_tv_premium_rate.id;

create sequence public.ins_pa_coverage_type_id_seq
as integer;

alter sequence public.ins_pa_coverage_type_id_seq owner to phillip;

alter sequence public.ins_pa_coverage_type_id_seq owned by public.ins_pa_coverage_type.id;

create sequence public.ins_pa_coverage_benefit_id_seq
as integer;

alter sequence public.ins_pa_coverage_benefit_id_seq owner to phillip;

alter sequence public.ins_pa_coverage_benefit_id_seq owned by public.ins_pa_coverage_benefit.id;

create sequence public.ins_tv_policy_id_seq
as integer;

alter sequence public.ins_tv_policy_id_seq owner to phillip;

alter sequence public.ins_tv_policy_id_seq owned by public.ins_tv_policy.id;

create sequence public.ins_pa_extension_selection_id_seq
as integer;

alter sequence public.ins_pa_extension_selection_id_seq owner to phillip;

alter sequence public.ins_pa_extension_selection_id_seq owned by public.ins_pa_extension_selection.id;

create sequence public.ins_tv_quotation_id_seq
as integer;

alter sequence public.ins_tv_quotation_id_seq owner to phillip;

alter sequence public.ins_tv_quotation_id_seq owned by public.ins_tv_quotation.id;

create sequence public.package_id_seq
as integer;

alter sequence public.package_id_seq owner to phillip;

alter sequence public.package_id_seq owned by public.package.id;

create sequence public.ins_tv_package_id_seq
as integer;

alter sequence public.ins_tv_package_id_seq owner to phillip;

alter sequence public.ins_tv_package_id_seq owned by public.ins_tv_package.id;

create sequence public.ins_pa_insured_person_aggregate_id_seq
as integer;

alter sequence public.ins_pa_insured_person_aggregate_id_seq owner to phillip;

alter sequence public.ins_pa_insured_person_aggregate_id_seq owned by public.ins_pa_insured_person_aggregate.id;

create sequence public.ins_tv_data_detail_id_seq
as integer;

alter sequence public.ins_tv_data_detail_id_seq owner to phillip;

alter sequence public.ins_tv_data_detail_id_seq owned by public.ins_tv_data_detail.id;

create sequence public.ins_pa_class_coverage_rate_id_seq
as integer;

alter sequence public.ins_pa_class_coverage_rate_id_seq owner to phillip;

alter sequence public.ins_pa_class_coverage_rate_id_seq owned by public.ins_pa_class_coverage_rate.id;

create sequence public.ins_tv_plan_zone_detail_id_seq
as integer;

alter sequence public.ins_tv_plan_zone_detail_id_seq owner to phillip;

alter sequence public.ins_tv_plan_zone_detail_id_seq owned by public.ins_tv_plan_zone_detail.id;

create sequence public.ins_pa_data_clause_id_seq;

alter sequence public.ins_pa_data_clause_id_seq owner to phillip;

alter sequence public.ins_pa_data_clause_id_seq owned by public.ins_pa_data_clause.id;

create sequence public.ins_tv_coverage_id_seq
as integer;

alter sequence public.ins_tv_coverage_id_seq owner to phillip;

alter sequence public.ins_tv_coverage_id_seq owned by public.ins_tv_coverage.id;

create sequence public.ins_tv_coverage_data_id_seq
as integer;

alter sequence public.ins_tv_coverage_data_id_seq owner to phillip;

alter sequence public.ins_tv_coverage_data_id_seq owned by public.ins_tv_coverage_data.id;

create sequence public.ins_tv_policy_commission_data_id_seq
as integer;

alter sequence public.ins_tv_policy_commission_data_id_seq owner to phillip;

alter sequence public.ins_tv_policy_commission_data_id_seq owned by public.ins_tv_policy_commission_data.id;

create sequence public.ins_pa_claim_id_seq
as integer;

alter sequence public.ins_pa_claim_id_seq owner to phillip;

alter sequence public.ins_pa_claim_id_seq owned by public.ins_pa_claim.id;

create sequence public.ins_pa_claim_generate_payment_or_claim_no_id_seq
as integer;

alter sequence public.ins_pa_claim_generate_payment_or_claim_no_id_seq owner to phillip;

alter sequence public.ins_pa_claim_generate_payment_or_claim_no_id_seq owned by public.ins_pa_claim_generate_payment_or_claim_no.id;

create sequence public.ins_pa_claim_detail_id_seq;

alter sequence public.ins_pa_claim_detail_id_seq owner to phillip;

alter sequence public.ins_pa_claim_detail_id_seq owned by public.ins_pa_claim_detail.id;

create sequence public.ins_tv_reinsurance_data_id_seq
as integer;

alter sequence public.ins_tv_reinsurance_data_id_seq owner to phillip;

alter sequence public.ins_tv_reinsurance_data_id_seq owned by public.ins_tv_reinsurance_data.id;

create sequence public.ins_tv_reinsurance_config_id_seq
as integer;

alter sequence public.ins_tv_reinsurance_config_id_seq owner to phillip;

alter sequence public.ins_tv_reinsurance_config_id_seq owned by public.ins_tv_reinsurance_config.id;

create sequence public.ins_tv_data_master_id_seq
as integer;

alter sequence public.ins_tv_data_master_id_seq owner to phillip;

alter sequence public.ins_tv_data_master_id_seq owned by public.ins_tv_data_master.id;

create sequence public.ins_pa_claim_schema_id_seq
as integer;

alter sequence public.ins_pa_claim_schema_id_seq owner to phillip;

alter sequence public.ins_pa_claim_schema_id_seq owned by public.ins_pa_claim_schema.id;

create sequence public.ins_pa_claim_schema_detail_id_seq
as integer;

alter sequence public.ins_pa_claim_schema_detail_id_seq owner to phillip;

alter sequence public.ins_pa_claim_schema_detail_id_seq owned by public.ins_pa_claim_schema_detail.id;

create sequence public.ins_translation_id_seq
as integer;

alter sequence public.ins_translation_id_seq owner to phillip;

alter sequence public.ins_translation_id_seq owned by public.ins_translation.id;

create sequence public.ins_tv_data_clause_id_seq;

alter sequence public.ins_tv_data_clause_id_seq owner to phillip;

alter sequence public.ins_tv_data_clause_id_seq owned by public.ins_tv_data_clause.id;

create sequence public.ins_tv_policy_invoice_note_id_seq
as integer;

alter sequence public.ins_tv_policy_invoice_note_id_seq owner to phillip;

alter sequence public.ins_tv_policy_invoice_note_id_seq owned by public.ins_tv_policy_invoice_note.id;

create sequence public.ins_tv_policy_endor_commission_hist_id_seq
as integer;

alter sequence public.ins_tv_policy_endor_commission_hist_id_seq owner to phillip;

alter sequence public.ins_tv_policy_endor_commission_hist_id_seq owned by public.ins_tv_policy_endor_commission_hist.id;

create sequence public.optional_extension_seq;

alter sequence public.optional_extension_seq owner to phillip;

create sequence public.ins_pa_extension_option_id_seq
as integer;

alter sequence public.ins_pa_extension_option_id_seq owner to phillip;

alter sequence public.ins_pa_extension_option_id_seq owned by public.ins_pa_extension_option.id;

create sequence public.ins_tv_claim_id_seq
as integer;

alter sequence public.ins_tv_claim_id_seq owner to phillip;

alter sequence public.ins_tv_claim_id_seq owned by public.ins_tv_claim.id;

create sequence public.ins_tv_claim_schema_id_seq
as integer;

alter sequence public.ins_tv_claim_schema_id_seq owner to phillip;

alter sequence public.ins_tv_claim_schema_id_seq owned by public.ins_tv_claim_schema.id;

create sequence public.ins_tv_claim_schema_detail_id_seq
as integer;

alter sequence public.ins_tv_claim_schema_detail_id_seq owner to phillip;

alter sequence public.ins_tv_claim_schema_detail_id_seq owned by public.ins_tv_claim_schema_detail.id;

create sequence public.ins_tv_claim_generate_payment_or_claim_no_id_seq
as integer;

alter sequence public.ins_tv_claim_generate_payment_or_claim_no_id_seq owner to phillip;

alter sequence public.ins_tv_claim_generate_payment_or_claim_no_id_seq owned by public.ins_tv_claim_generate_payment_or_claim_no.id;

create sequence public.ins_pa_claim_payment_detail_id_seq
as integer;

alter sequence public.ins_pa_claim_payment_detail_id_seq owner to phillip;

alter sequence public.ins_pa_claim_payment_detail_id_seq owned by public.ins_pa_claim_payment_detail.id;

create sequence public.ins_pa_claim_payment_id_seq
as integer;

alter sequence public.ins_pa_claim_payment_id_seq owner to phillip;

alter sequence public.ins_pa_claim_payment_id_seq owned by public.ins_pa_claim_payment.id;

create sequence public.ins_tv_country_id_seq
as integer;

alter sequence public.ins_tv_country_id_seq owner to phillip;

alter sequence public.ins_tv_country_id_seq owned by public.ins_tv_country.id;

create sequence public.ins_tv_deductible_id_seq
as integer;

alter sequence public.ins_tv_deductible_id_seq owner to phillip;

alter sequence public.ins_tv_deductible_id_seq owned by public.ins_tv_deductible.id;

create sequence public.ins_tv_deductible_data_id_seq
as integer;

alter sequence public.ins_tv_deductible_data_id_seq owner to phillip;

alter sequence public.ins_tv_deductible_data_id_seq owned by public.ins_tv_deductible_data.id;

create sequence public.ins_tv_claim_detail_id_seq;

alter sequence public.ins_tv_claim_detail_id_seq owner to phillip;

alter sequence public.ins_tv_claim_detail_id_seq owned by public.ins_tv_claim_detail.id;

create sequence public.sm_user_id_seq1;

alter sequence public.sm_user_id_seq1 owner to phillip;

alter sequence public.sm_user_id_seq1 owned by public.sm_user.id;

create sequence public.ins_tv_claim_payment_id_seq
as integer;

alter sequence public.ins_tv_claim_payment_id_seq owner to phillip;

alter sequence public.ins_tv_claim_payment_id_seq owned by public.ins_tv_claim_payment.id;

create sequence public.ins_tv_claim_payment_detail_id_seq
as integer;

alter sequence public.ins_tv_claim_payment_detail_id_seq owner to phillip;

alter sequence public.ins_tv_claim_payment_detail_id_seq owned by public.ins_tv_claim_payment_detail.id;

