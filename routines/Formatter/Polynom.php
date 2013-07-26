<?php
class Formatter_Polynom
{
    const FORMAT_STRING             = 'raw';
    const FORMAT_CODE_PHP           = 'php';
    
    const CONTAINER_COEFFICIENT     = 'container_coef';
    const CONTAINER_POWER           = 'container_power';
    const CONTAINER_MEMBER          = 'container_member';
    const DELIMITER_COEFFICIENT     = 'delim_coef';
    const DELIMITER_MEMBER          = 'delim_member';
    const DELIMITER_POWER           = 'delim_power';
    const ENCLOSER_POWER_LEFT       = 'enc_power_left';
    const ENCLOSER_POWER_RIGHT      = 'enc_power_right';
    const ENCLOSER_MEMBER_LEFT      = 'enc_member_left';
    const ENCLOSER_MEMBER_RIGHT     = 'enc_member_right';
    const ENCLOSER_COEFFICIENT_LEFT = 'enc_coef_left';
    const ENCLOSER_COEFFICIENT_RIGHT= 'enc_coef_right';
    
    public static function getPolynomFormat($sFormatType=self::FORMAT_STRING)
    {
        $rgFormat = [
            self::FORMAT_STRING => [
                self::DELIMITER_COEFFICIENT     => '*',
                self::DELIMITER_MEMBER          => '+',
                self::DELIMITER_POWER           => '^',
                self::CONTAINER_COEFFICIENT     => '{coef}',
                self::CONTAINER_MEMBER          => '{member}',
                self::CONTAINER_POWER           => '{power}',
                self::ENCLOSER_COEFFICIENT_LEFT => '(',
                self::ENCLOSER_COEFFICIENT_RIGHT=> ')',
                self::ENCLOSER_MEMBER_LEFT      => '(',
                self::ENCLOSER_MEMBER_RIGHT     => ')',
                self::ENCLOSER_POWER_LEFT       => '(',
                self::ENCLOSER_POWER_RIGHT      => ')'
            ],
            self::FORMAT_CODE_PHP => [

            ]
        ];
        if(!array_key_exists($sFormatType, $rgFormat))
        {
            throw new Exception('Format "'.$sFormatType.'" has no corresponding formatter');
        }
        return $rgFormat[$sFormatType];
    }
}