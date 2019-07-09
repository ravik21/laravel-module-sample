<?php

namespace Modules\User\Entities;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\NamespacedEntity;
use Modules\Media\Helpers\FileHelper;
use Modules\Media\Image\Facade\Imagy;
use Modules\Media\ValueObjects\MediaPath;

class File extends Model implements Responsable
{
    use NamespacedEntity;
    /**
     * All the different images types where thumbnails should be created
     * @var array
     */

    private $imageExtensions      = ['jpg', 'png', 'jpeg', 'gif'];
    private $applicationExtensions = ['pdf', 'docx'];

    protected $fillable = [];
    protected $table = 'files';
    protected $guarded = [];

    protected $appends = ['path_string', 'media_type'];
    protected $casts = ['is_folder' => 'boolean',];
    protected static $entityNamespace = 'user';

    public function parent_folder()
    {
        return $this->belongsTo(__CLASS__, 'folder_id');
    }

    public function getPathAttribute($value)
    {
        return new MediaPath($value);
    }

    public function getPathStringAttribute()
    {
        return (string) $this->path;
    }

    public function getMediaTypeAttribute()
    {
        return FileHelper::getTypeByMimetype($this->mime_type);
    }

    public function isFolder(): bool
    {
        return $this->is_folder;
    }

    public function isImage()
    {
        return in_array(pathinfo($this->path, PATHINFO_EXTENSION), $this->imageExtensions);
    }

    public function isApplication()
    {
        return in_array(pathinfo($this->path, PATHINFO_EXTENSION), $this->applicationExtensions);
    }

    public function getThumbnail($type)
    {
        if ($this->isImage() && $this->getKey()) {
            return Imagy::getThumbnail($this->path, $type);
        }

        if ($this->isApplication()) {
            return '';
        }

        // $extension = 'jpg';

        // $path = sprintf("%s/%s_videoThumb.%s", pathinfo($this->path->getRelativeUrl(), PATHINFO_DIRNAME), pathinfo($this->path->getRelativeUrl(), PATHINFO_FILENAME), $extension);

        // return $path;

        return Imagy::getThumbnail($this->getVideoThumbnailPath(), $type);
    }

    /**
     * Create an HTTP response that represents the object.
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return response()
            ->file(public_path($this->path->getRelativeUrl()), [
                'Content-Type' => $this->mimetype,
            ]);
    }

    public function getVideoThumbnailPath()
    {
        $extension = 'jpg';

        $path = sprintf("%s/%s.%s", pathinfo($this->path->getRelativeUrl(), PATHINFO_DIRNAME), pathinfo($this->path->getRelativeUrl(), PATHINFO_FILENAME), $extension);

        return new MediaPath($path);
    }

    public function getPath()
    {
        if ($this->is_folder) {
            return $this->path->getRelativeUrl();
        }

        return (string) $this->path;
    }

}
