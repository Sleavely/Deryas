<?php
    error_reporting(0);
    $files_path = "files/";
    $spr_path = $files_path.'Tibia.spr';
    $dat_path = $files_path.'Tibia.dat';
    $otb_path = $files_path.'items.otb';
    $caching = true;

    define('HEX_PREFIX', '0x');
    settype(($myId = $_GET['id']), 'integer');

    $cache_path = "images/sprites";
    $img_path = "images/sprites/$myId.gif"; //only if caching

    if ($caching && file_exists($img_path))
    {
        $spr = imagecreatefromgif($img_path);
    }
    else
    {
        if ($caching && !file_exists($cache_path)) mkdir($cache_path);

        if ($myId < 100)
            trigger_error('Item id must be a number above 100', E_USER_ERROR);

        $fp = fopen($otb_path, 'rb') or exit;

        while (false !== ($char = fgetc($fp)))
        {
            $optByte = HEX_PREFIX.bin2hex($char);
            if ($optByte == 0xFE)
                $init = true;
            elseif ($optByte == 0x10 && $init)
            {
                extract(unpack('x2/Ssid', fread($fp, 4)));
                if ($myId == $sid)
                {
                    if (HEX_PREFIX.bin2hex(fread($fp, 1)) == 0x11)
                        extract(unpack('x2/SmyId', fread($fp, 4)));
                    break;
                }
                $init = false;
            }
        }

        fclose($fp);

        $fp = fopen($dat_path, 'rb') or exit;
        $maxId = array_sum(unpack('x4/S*', fread($fp, 12)));

        if ($myId > $maxId)
            trigger_error(sprintf('Out of range', ftell($fp)), E_USER_ERROR);

        for ($id = 100 /* Void */; $id <= $myId; $id++)
        {
            while (($optByte = HEX_PREFIX.bin2hex(fgetc($fp))) != 0xFF)
            {
                $offset = 0;
                switch ($optByte)
                {
                    case 0x00:case 0x09:
                    case 0x0A:case 0x1A:
                    case 0x1D:case 0x1E:
                        $offset = 2;
                    break;

                    case 0x16:case 0x19:
                        $offset = 4;
                    break;

                    case 0x01:case 0x02:case 0x03:case 0x04:case 0x05:
                    case 0x06:case 0x07:case 0x08:case 0x0B:case 0x0C:
                    case 0x0D:case 0x0E:case 0x0F:case 0x10:case 0x11:
                    case 0x12:case 0x13:case 0x14:case 0x15:case 0x17:
                    case 0x18:case 0x1B:case 0x1C:case 0x1F:case 0x20:
                    break;

                    default:
                        trigger_error(sprintf('Unknown dat opt byte: %s (previous opt byte: %s; address: %x)', $optByte, $prevByte, ftell($fp)), E_USER_ERROR);
                    break;
                }
                $prevByte = $optByte;
                fseek($fp, $offset, SEEK_CUR);
            }

            extract(unpack('Cwidth/Cheight', fread($fp, 2)));

            if ($width > 1 || $height > 1)
            {
                fseek($fp, 1, SEEK_CUR);
                $nostand = true;
            }

            $spr_count = array_product(unpack('C*', fread($fp, 5))) * $width * $height;
            $sprites = unpack('S*', fread($fp, 2 * $spr_count));
        }

        fclose($fp);

        $fp = fopen($spr_path, 'rb');

        if ($nostand)
        {
            for ($i = 0; $i < sizeof($sprites)/4; $i++)
                $spriteIds = array_merge((array)$spriteIds, array_reverse(array_slice($sprites, $i*4, 4)));
        }
        else
            $spriteIds = (array) $sprites[array_rand($sprites)];

        fseek($fp, 6);

        $spr = imagecreatetruecolor(32 * $width, 32 * $height);
        imagecolortransparent($spr, imagecolorallocate($spr, 0, 0, 0));

        foreach ($spriteIds as $k => $id)
        {
            if ($id < 100) continue;

            fseek($fp, 6 + ($id - 1) * 4);

            extract(unpack('Laddress', fread($fp, 4)));
            fseek($fp, $address + 3);

            extract(unpack('Ssize', fread($fp, 2)));

            $num = 0;
            $bit = 0;

            while ($bit < $size)
            {
                $pixels = unpack('Strans/Scolored', fread($fp, 4));
                $num += $pixels['trans'];

                for ($i = 0; $i < $pixels['colored']; $i++)
                {
                    extract(unpack('Cred/Cgreen/Cblue', fread($fp, 3)));

                    $red = $red == 0 ? ($green == 0 ? ($blue == 0 ? 1 : $red) : $red) : $red;
                    imagesetpixel($spr, $num % 32 + ($k % 2 == 1 ? 32 : 0), $num / 32 + ($k % 4 != 1 && $k % 4 != 0 ? 32 : 0), imagecolorallocate($spr, $red, $green, $blue));
                    $num++;
                }

                $bit += 4 + 3 * $pixels['colored'];
            }
        }

        fclose($fp);

        if ($caching && !file_exists($img_path)) imagegif($spr, $img_path);
    }

    header('Content-type: image/gif');

    imagegif($spr);
    imagedestroy($spr);
?>
