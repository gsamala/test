<?php

namespace Kemana\Abdn\Model;

class Config
{
	const IN_DAYS 			= 0;
    const IN_HOURS 			= 1;
	const UNIT 		 		= "kemanaabdn/general/unit";
    const CUSTOMER_GROUPS	= "kemanaabdn/general/customer";
    const ACTIVE   			= "kemanaabdn/general/enable";
    const MAX_NUMBER_TIMES  = "kemanaabdn/general/max_number_email";
    const REMINDER_TIME    = "kemanaabdn/general/reminder_time";

    const SENDER_NAME 		= "kemanaabdn/email/sender_name";
    const SENDER_EMAIL 		= "kemanaabdn/email/sender_email";
    const EMAIL_SUBJECT 	= "kemanaabdn/email/subject";
    const EMAIL_TEMPLATE    = 'kemanaabdn/email/email_template';
}