<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

class DirectoryController extends Controller
{
    /**
     * Sets the path for archives and queries or configures S3 storage.
     *
     * Validates the request and determines whether to use the local system or S3 storage.
     * Updates the database with the provided path or S3 credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setDirectory(Request $request)
    {
        $validated = $request->validate([
            'archives_path' => 'required_if:disk,system|string',
            'query_path' => 'required_if:disk,system|string',
            'disk' => 'required|in:s3,system',
            's3_bucket' => 'required_if:disk,s3|string',
            's3_region' => 'required_if:disk,s3|string',
            's3_access_key' => 'required_if:disk,s3|string',
            's3_secret_key' => 'required_if:disk,s3|string',
            's3_endpoint_url' => 'nullable|string',
        ]);

        match ($validated['disk']) {
            's3' => $this->setS3path(
                $validated['s3_bucket'],
                $validated['s3_region'],
                $validated['s3_access_key'],
                $validated['s3_secret_key'],
                $validated['s3_endpoint_url'] ?? null
            ),
            'system' => $this->setDirectoryPath(
                $validated['archives_path'],
                $validated['query_path']
            )
        };

        return successResponse(Lang::get('lang.product_config_success'));
    }

    /**
     * Configures the S3 storage path and credentials.
     *
     * Updates the database with the S3 storage settings.
     *
     * @param string $s3Bucket
     * @param string $s3Region
     * @param string $s3AccessKey
     * @param string $s3SecretKey
     * @param string|null $s3EndpointUrl
     * @return void
     */
    protected function setS3path($s3Bucket, $s3Region, $s3AccessKey, $s3SecretKey, $s3EndpointUrl)
    {
        DB::table('directory')->where('id', DB::table('directory')->min('id'))->update([
            'disk' => 's3',
            's3_bucket' => $s3Bucket,
            's3_region' => $s3Region,
            's3_access_key' => $s3AccessKey,
            's3_secret_key' => $s3SecretKey,
            's3_endpoint_url' => $s3EndpointUrl,
        ]);
    }

    /**
     * Sets the directory paths for archives and queries.
     *
     * Updates the database with the local system directory paths.
     *
     * @param string $archives
     * @param string $queries
     * @return void
     */
    protected function setDirectoryPath($archives, $queries)
    {
        DB::table('directory')->where('id', DB::table('directory')->min('id'))->update([
            'disk' => 'system',
            'ARCHIVES_DIRECTORY' => $archives,
            'QUERIES_DIRECTORY' => $queries,
        ]);
    }

    /**
     * Retrieves the directory configuration.
     *
     * Returns the current directory configuration from the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDirectory()
    {
        $directory = DB::table('directory')->first();

        if ($directory) {
            $directory = collect($directory)->map(function ($value) {
                return $value === null ? '' : $value;
            })->toArray();
        }

        return successResponse(Lang::get('lang.directory_show'), $directory);
    }

    /**
     * Configures the S3 storage driver.
     *
     * Reads the S3 settings from the database and builds an S3 storage configuration.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function configureS3()
    {
        $config = DB::table('directory')->where('disk', 's3')->first();
        return Storage::build([
            'driver' => 's3',
            'key' => $config->s3_access_key,
            'secret' => $config->s3_secret_key,
            'region' => $config->s3_region,
            'bucket' => $config->s3_bucket,
            'endpoint' => $config->s3_endpoint_url,
            'url' => null,
            'use_path_style_endpoint' => false,
            'throw' => false,
        ]);
    }

    /**
     * Checks if the current storage is S3.
     *
     * Determines whether the storage configuration is set to use S3.
     *
     * @return bool
     */
    public function isS3Storage()
    {
        $config = DB::table('directory')->first();
        return $config->disk === 's3';
    }
}
