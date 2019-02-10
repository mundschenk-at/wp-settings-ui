<?php
/**
 *  This file is part of WordPress Settings UI.
 *
 *  Copyright 2017-2019 Peter Putzer.
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 *  ***
 *
 *  @package mundschenk-at/wp-settings-ui
 *  @license http://www.gnu.org/licenses/gpl-2.0.html
 */

$outer_attributes = $this->get_outer_html_attributes(); // These are already escaped.
$outer_attributes = empty( $outer_attributes ) ? '' : " {$outer_attributes}";

$control_id = $this->get_id();

?>
<?php if ( ! empty( $this->grouped_controls ) ) : ?>
	<fieldset<?php echo $outer_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<legend class="screen-reader-text"><?php echo \esc_html( $this->short ); ?></legend>
<?php else : ?>
	<div<?php echo $outer_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<?php endif; // grouped_controls. ?>

<?php if ( ! empty( $this->label ) ) : ?>
	<label for="<?php echo \esc_attr( $control_id ); ?>"><?php echo \wp_kses( $this->get_label(), self::ALLOWED_HTML ); ?></label>
<?php elseif ( $this->has_inline_help() ) : ?>
	<label for="<?php echo \esc_attr( $control_id ); ?>">
<?php endif; ?>

<?php if ( ! $this->label_has_placeholder() ) : ?>
		<?php $this->render_element(); // Control-specific markup. ?>
<?php endif; ?>

<?php if ( $this->has_inline_help() ) : ?>
	<span id="<?php echo esc_attr( $control_id ); ?>-description" class="description"><?php echo \wp_kses( $this->help_text, self::ALLOWED_DESCRIPTION_HTML ); ?></span></label>
<?php elseif ( ! empty( $this->help_text ) ) : ?>
	<p id="<?php echo esc_attr( $control_id ); ?>-description" class="description"><?php echo \wp_kses( $this->help_text, self::ALLOWED_DESCRIPTION_HTML ); ?></p>
<?php endif; ?>

<?php if ( ! empty( $this->grouped_controls ) ) : ?>
	<?php foreach ( $this->grouped_controls as $control ) : ?>
		<br />
		<?php $control->render(); ?>
	<?php endforeach; ?>
	</fieldset>
<?php else : ?>
	</div>
<?php endif; // grouped_controls. ?>
<?php
