<?php

/*
	Pehr Johansson

	File: qa-plugin/qa-tag-email-notifications/qa-tag-email-notifications-event.php
	Version: 0.9
	Date: 2013-02-21
	Description: Event module class for tag email notifications plugin
*/

class qa_tag_email_notifications_event
{
    function process_event($event, $userid, $handle, $cookieid, $params)
    {
        require_once QA_INCLUDE_DIR.'qa-app-emails.php';
        require_once QA_INCLUDE_DIR.'qa-app-format.php';
        require_once QA_INCLUDE_DIR.'qa-util-string.php';
        require_once QA_INCLUDE_DIR.'qa-db.php';

        switch ($event) {
        case 'q_post':
            if (empty($params['tags'])) { return; } else {
                $arrTags = explode(',', $params['tags']);
            }
            $tagid = qa_db_single_select(
                                         array(
                                               'columns' => array('^words.wordid', '^words.word'),
                                               'source' => "^words WHERE word IN ('" . implode($arrTags, "','") . "') AND tagcount>0",
                                               )
                                         );
            $emails = array();
            for ($i = 0; $i < count($tagid); $i++) {
                $tmpArr = qa_db_select_with_pending($this->qa_db_tag_favorite_emails_selectspec($tagid[$i]['wordid']));
                foreach ($tmpArr as $k=>$v) {
                    array_push($emails, $v);
                }
            }
            for ($i = 0; $i < count($emails); $i++)
                {
                    $bcclist = array();
                    for ($j = 0; $j < 75 && $i < count($emails); $j++, $i++)
                        {
                            $bcclist[$emails[$i]['email']] = $emails[$i]['email'];
                        }
                    $this->tag_email_notification_send_notification($bcclist, null, null, qa_lang('emails/q_posted_subject_tag'), qa_lang('emails/q_posted_body'), array(
                                                                                                                                                                         '^q_handle' => isset($handle) ? $handle : qa_lang('main/anonymous'),
                                                                                                                                                                         '^q_title' => $params['title'], // don't censor title or content here since we want the admin to see bad words
                                                                                                                                                                         '^q_content' => $params['text'],
                                                                                                                                                                         '^url' => qa_q_path($params['postid'], $params['title'], true),
                                                                                                                                                                         ));
                }
            break;
        }

    }

    function qa_db_tag_favorite_emails_selectspec($tagid)
    /*
     */
    {
        require_once QA_INCLUDE_DIR.'qa-app-updates.php';

        return array(
                     'columns' => array('^users.email'),
                     'source' => "^users JOIN ^userfavorites ON ^userfavorites.userid=^users.userid WHERE ^userfavorites.entityid=$ AND ^userfavorites.entitytype=$",
                     'arguments' => array($tagid, QA_ENTITY_TAG),
                     'sortasc' => 'title',
                     );
    }
    function tag_email_notification_send_notification($bcclist, $email, $handle, $subject, $body, $subs)
    /*
     */
    {
        if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }

        global $qa_notifications_suspended;

        if ($qa_notifications_suspended>0)
            return false;

        require_once QA_INCLUDE_DIR.'qa-db-selects.php';
        require_once QA_INCLUDE_DIR.'qa-util-string.php';

        $subs['^site_title']=qa_opt('site_title');
        $subs['^handle']=$handle;
        $subs['^email']=$email;
        $subs['^open']="\n";
        $subs['^close']="\n";

        return $this->tag_email_send_email(array(
                                                 'fromemail' => qa_opt('from_email'),
                                                 'fromname' => qa_opt('site_title'),
                                                 'toemail' => $email,
                                                 'toname' => $handle,
                                                 'bcclist' => $bcclist,
                                                 'subject' => strtr($subject, $subs),
                                                 'body' => (empty($handle) ? '' : qa_lang_sub('emails/to_handle_prefix', $handle)).strtr($body, $subs),
                                                 'html' => false,
                                                 ));
    }


    function tag_email_send_email($params)
    /*
     */
    {
        if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }

        require_once QA_INCLUDE_DIR.'qa-class.phpmailer.php';

        $mailer=new PHPMailer();
        $mailer->CharSet='utf-8';

        $mailer->From=$params['fromemail'];
        $mailer->Sender=$params['fromemail'];
        $mailer->FromName=$params['fromname'];
        if (isset($params['toemail']))
            {
                $mailer->AddAddress($params['toemail'], $params['toname']);
            }
        $mailer->Subject=$params['subject'];
        $mailer->Body=$params['body'];
        if (isset($params['bcclist']))
            {
                foreach ($params['bcclist'] as $email)
                    $mailer->AddBCC($email);
            }

        if ($params['html'])
            $mailer->IsHTML(true);

        if (qa_opt('smtp_active')) {
            $mailer->IsSMTP();
            $mailer->Host=qa_opt('smtp_address');
            $mailer->Port=qa_opt('smtp_port');

            if (qa_opt('smtp_secure'))
                $mailer->SMTPSecure=qa_opt('smtp_secure');

            if (qa_opt('smtp_authenticate')) {
                $mailer->SMTPAuth=true;
                $mailer->Username=qa_opt('smtp_username');
                $mailer->Password=qa_opt('smtp_password');
            }
        }

        return $mailer->Send();
    }

};


/*
  Omit PHP closing tag to help avoid accidental output
*/
