/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */

import MenuItemType from './MenuItemType'

export default class ContentMenuItemType extends MenuItemType {
  /**
     * @inheritDoc
     */
  getTreeType () {
    return {
      icon: 'oi oi-document',
      max_children: -1,
      max_depth: -1,
      valid_children: -1
    }
  }
}
