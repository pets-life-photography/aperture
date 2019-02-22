<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use Picqer\Financials\Moneybird\Entities\Contact;

interface ContactSynchronizerInterface
{
    /**
     * Determine whether the given contact is candidate to become a client.
     *
     * @param Contact $contact
     *
     * @return bool
     */
    public function isCandidate(Contact $contact): bool;

    /**
     * Synchronize the given contact with local clients.
     *
     * @param Contact $contact
     *
     * @return void
     */
    public function synchronize(Contact $contact): void;
}
