<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Ponder Source Foundation <michiel@pondersource.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\PreJournal\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'prejournal';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
