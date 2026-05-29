<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260528153045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add onboarding fields on user (signup_intent, signup_intent_other, signup_intent_at, onboarding_version) and contextual AI opt-in tracking (consent_ai_optin_at, consent_ai_optin_trigger).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD signup_intent VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD signup_intent_other VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD signup_intent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD onboarding_version VARCHAR(8) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD consent_ai_optin_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD consent_ai_optin_trigger VARCHAR(32) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" DROP signup_intent');
        $this->addSql('ALTER TABLE "user" DROP signup_intent_other');
        $this->addSql('ALTER TABLE "user" DROP signup_intent_at');
        $this->addSql('ALTER TABLE "user" DROP onboarding_version');
        $this->addSql('ALTER TABLE "user" DROP consent_ai_optin_at');
        $this->addSql('ALTER TABLE "user" DROP consent_ai_optin_trigger');
    }
}
