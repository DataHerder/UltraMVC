<?php 
/**
 * UltraMVC
 * A fast lightweight Model View Controller framework
 * 
 * Copyright (C) 2015 Paul Carlton
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @author      Paul Carlton
 * @category    UltraMVC
 * @package     Framework/Mail
 * @license     GNU license
 * @version     1.0
 * @link        my.public.repo
 * @since       File available since
 */

namespace UltraMVC\Framework\Mailer\Abstracts;

interface MailerInterface {
	public function send();
	public function setFrom($from);
	public function setTo($to);
	public function setReplyTo($replyTo);
	public function setCC($cc);
	public function setBCC($bcc);
}
