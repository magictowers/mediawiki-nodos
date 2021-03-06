<?php
/**
 * Represents a page section in a user-defined form.
 * This class should really be called "SFPageSectionInForm", to differentiate
 * it from the SFWikiPageSection class.
 *
 * @author Himeshi
 * @file
 * @ingroup SF
 */
class SFPageSection {
	private $mSectionName;
	private $mSectionLevel = 2;
	private $mIsMandatory = false;
	private $mIsHidden = false;
	private $mIsRestricted = false;
	private $mSectionArgs = array();

	static function create( $section_name ) {
		$ps = new SFPageSection();
		$ps->mSectionName = $section_name;

		return $ps;
	}

	static function newFromFormTag( $tag_components ) {
		global $wgUser;

		$ps = new SFPageSection();
		$ps->mSectionName = trim( $tag_components[1] );

		// cycle through the other components
		for ( $i = 2; $i < count( $tag_components ); $i++ ) {

			$component = trim( $tag_components[$i] );

			if ( $component === 'mandatory' ) {
				$ps->mIsMandatory = true;
			} elseif ( $component === 'hidden' ) {
				$ps->mIsHidden = true;
			} elseif ( $component === 'restricted' ) {
				$ps->mIsRestricted = !( $wgUser && $wgUser->isAllowed( 'editrestrictedfields' ) );
			} elseif ( $component === 'autogrow' ) {
				$ps->mSectionArgs['autogrow'] = true;
			}

			$sub_components = array_map( 'trim', explode( '=', $component, 2 ) );

			if ( count( $sub_components ) === 2 ) {
				switch ( $sub_components[0] ) {
				case 'level':
					$ps->mSectionLevel = $sub_components[1];
					break;
				case 'rows':
				case 'cols':
				case 'class':
				case 'editor':
					$ps->mSectionArgs[$sub_components[0]] = $sub_components[1];
					break;
				default:
					// Ignore unknown
				}
			}
		}
		return $ps;
	}


	public function getSectionName() {
		return $this->mSectionName;
	}

	public function getSectionLevel() {
		return $this->mSectionLevel;
	}

	public function setSectionLevel( $section_level ) {
		$this->mSectionLevel = $section_level;
	}

	public function setIsMandatory( $isMandatory ) {
		$this->mIsMandatory = $isMandatory;
	}

	public function isMandatory() {
		return $this->mIsMandatory;
	}

	public function setIsHidden( $isHidden ) {
		$this->mIsHidden = $isHidden;
	}

	public function isHidden() {
		return $this->mIsHidden;
	}

	public function setIsRestricted( $isRestricted ) {
		$this->mIsRestricted = $isRestricted;
	}

	public function isRestricted() {
		return $this->mIsRestricted;
	}

	public function setSectionArgs( $key, $value ) {
		$this->mSectionArgs[$key] = $value;
	}

	public function getSectionArgs() {
		return $this->mSectionArgs;
	}

	function creationHTML( $section_count ) {
		global $wgRequest;
		$paramValues = array();
		$section_name = $this->mSectionName;
		$section_level = $this->mSectionLevel;

		$section_str = wfMessage( 'sf_createform_pagesection' )->text() . " '" . $section_name . "'";
		$text = Html::hidden( "section_$section_count", $section_name );
		$text .= '<div class="sectionForm">';
		$text .= Html::element( 'h2', array(), $section_str );

		foreach ( $wgRequest->getValues() as $key => $value ) {
			if ( ( $pos = strpos( $key, '_section_'.$section_count ) ) != false ) {
				$paramName = substr( $key, 0, $pos );
				$paramName = str_replace( '_', ' ', $paramName );
				$paramValues[$paramName] = $value;
			}
		}

		$header_options =  '';
		$text .= Html::element( 'span', null, wfMessage( 'sf_createform_sectionlevel' )->text() ) . "\n";
		for ( $i = 1; $i < 7; $i++ ) {
			if ( $section_level == $i ) {
				$header_options .= " " . Html::element( 'option', array( 'value' => $i, 'selected' ), $i ) . "\n";
			} else {
				$header_options .= " " . Html::element( 'option', array( 'value' => $i ), $i ) . "\n";
			}
		}
		$text .= Html::rawElement( 'select', array( 'name' => "level_section_" . $section_count ), $header_options ) . "\n";
		$other_param_text = wfMessage( 'sf_createform_otherparameters' )->escaped();
		$text .= "<fieldset class=\"sfCollapsibleFieldset\"><legend>$other_param_text</legend>\n";
		$text .= Html::rawElement( 'div', array(),
		SFCreateForm::showSectionParameters( $section_count, $paramValues ) ) . "\n";
		$text .= "</fieldset>\n";
		$removeSectionButton = Html::input( 'delsection_' . $section_count, wfMessage( 'sf_createform_removesection' )->text(), 'submit' ) . "\n";
		$text .= "</br>" . Html::rawElement( 'p', null, $removeSectionButton ) . "\n";
		$text .= "	</div>\n";

		return $text;
	}

	function createMarkup() {
		$section_name = $this->mSectionName;
		$section_level = $this->mSectionLevel;
		// Set default section level to 2
		if ( $section_level == '' ){
			$section_level = 2;
		}
		//display the section headers in wikitext
		$header_string = "";
		$header_string .= str_repeat( "=", $section_level );
		$text = $header_string . $section_name . $header_string . "\n";

		$text .= "{{{section|" . $section_name . "|level=" . $section_level;

		if ( $this->mIsMandatory ) {
			$text .= "|mandatory";
		} elseif ( $this->mIsRestricted ) {
			$text .= "|restricted";
		} elseif ( $this->mIsHidden ) {
			$text .= "|hidden";
		}
		foreach ( $this->mSectionArgs as $arg => $value ) {
			if ( $value === true ) {
				$text .= "|$arg";
			} else {
				$text .= "|$arg=$value";
			}
		}
		$text .= "}}}\n";

		return $text;
	}

	public static function getParameters() {
		$params = array();

		$params['mandatory'] = array(
			'name' => 'mandatory',
			'type' => 'boolean',
			'description' => wfMessage( 'sf_forminputs_mandatory' )->text()
		);
		$params['restricted'] = array(
			'name' => 'restricted',
			'type' => 'boolean',
			'description' => wfMessage( 'sf_forminputs_restricted' )->text()
		);
		$params['hidden'] = array(
			'name' => 'hidden',
			'type' => 'boolean',
			'description' => wfMessage( 'sf_createform_hiddensection' )->text()
		);
		$params['class'] = array(
			'name' => 'class',
			'type' => 'string',
			'description' => wfMessage( 'sf_forminputs_class' )->text()
		);
		$params['rows'] = array(
			'name' => 'rows',
			'type' => 'int',
			'description' => wfMessage( 'sf_forminputs_rows' )->text()
		);
		$params['cols'] = array(
			'name' => 'cols',
			'type' => 'int',
			'description' => wfMessage( 'sf_forminputs_cols' )->text()
		);
		$params['autogrow'] = array(
			'name' => 'autogrow',
			'type' => 'boolean',
			'description' => wfMessage( 'sf_forminputs_autogrow' )->text()
		);

		return $params;
	}

	function getParamValues() {
		$paramValues = array();
		$paramValues['restricted'] = $this->mIsRestricted;
		$paramValues['hidden'] =  $this->mIsHidden;
		$paramValues['mandatory'] = $this->mIsMandatory;
		return $paramValues;
	}
}
