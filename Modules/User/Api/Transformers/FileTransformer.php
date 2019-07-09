<?php namespace Modules\User\Api\Transformers;

use Modules\User\Entities\File;
use League\Fractal\TransformerAbstract;
use Modules\Media\Helpers\FileHelper;

class FileTransformer extends TransformerAbstract
{
    /**
     * Transform Media File.
     *
     * @param File $File
     * @return League\Fractal\ItemResource
     */
    public function transform(File $file)
    {
        $transformed = [
            'id'                => $file->id,
            'original_filename' => $file->name,
            'path'              => $file->getPath(),
            'media_type'        => $file->getMediaTypeAttribute(),
            'fa_icon'           => FileHelper::getFaIcon($file->getMediaTypeAttribute(), $file->name),
            'width'             => $file->width,
            'height'            => $file->height,
            'is_image'          => $file->isImage(),
            'is_application'    => $file->isApplication(),
            'created_at'        => $file->created_at->format('H:i:s d/m/Y')
        ];

        if ($transformed['is_image']) {
            $transformed['small_thumb']  = $file->getThumbnail('smallThumb');
            $transformed['medium_thumb'] = $file->getThumbnail('mediumThumb');
            $transformed['large_thumb']  = $file->getThumbnail('largeThumb');
        } elseif ($transformed['is_application']) {
           $transformed['small_thumb']  = '';
        } else {
            $transformed['video_thumb'] = $file->getThumbnail('videoThumb');
        }

        return $transformed;
    }
}
