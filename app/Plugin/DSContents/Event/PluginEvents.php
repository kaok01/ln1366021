<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2016 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\DSContents\Event;

final class PluginEvents
{
    const ADMIN_CONTENT_PAGE_INDEX_COMPLETE = 'admin.plugin.dscontents.content.page.index.initialize';
    const ADMIN_CONTENT_PAGE_EDIT_INITIALIZE = 'admin.plugin.dscontents.content.page.edit.initialize';
    const ADMIN_CONTENT_PAGE_EDIT_COMPLETE = 'admin.plugin.dscontents.content.page.edit.complete';
    const ADMIN_CONTENT_PAGE_DELETE_COMPLETE = 'admin.plugin.dscontents.content.page.delete.complete';

    const ADMIN_CONTENT_BLOCK_INDEX_COMPLETE = 'admin.plugin.dscontents.content.block.index.complete';
    const ADMIN_CONTENT_BLOCK_EDIT_INITIALIZE = 'admin.plugin.dscontents.content.block.edit.initialize';
    const ADMIN_CONTENT_BLOCK_EDIT_COMPLETE = 'admin.plugin.dscontents.content.block.edit.complete';
    const ADMIN_CONTENT_BLOCK_DELETE_COMPLETE = 'admin.plugin.dscontents.content.block.delete.complete';
}
