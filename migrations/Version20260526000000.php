<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260526000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add RGPD consent fields on user (consent_at, consent_version, consent_ai_optin).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD consent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD consent_version VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD consent_ai_optin BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" DROP consent_ai_optin');
        $this->addSql('ALTER TABLE "user" DROP consent_version');
        $this->addSql('ALTER TABLE "user" DROP consent_at');
    }
}
