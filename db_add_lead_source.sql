ALTER TABLE bs_reg_new_users
    ADD COLUMN lead_source VARCHAR(32) NULL AFTER club,
    ADD INDEX idx_bs_reg_new_users_lead_source (club, lead_source, date_reg);
