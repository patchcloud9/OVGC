<?php

namespace App\Models;

/**
 * GalleryImage Model
 * 
 * Represents an image in the gallery.
 * 
 * Usage:
 *   $image = GalleryImage::find(1);
 *   $images = GalleryImage::all();
 *   $image = GalleryImage::create([
 *       'title' => 'Sunset',
 *       'description' => 'Beautiful sunset photo',
 *       'filename' => 'sunset.jpg',
 *       'file_path' => '/uploads/gallery/sunset.jpg',
 *       'uploaded_by' => 1
 *   ]);
 */
class GalleryImage extends Model
{
    protected string $table = 'gallery_images';
    
    protected array $fillable = [
        'title',
        'description',
        'filename',
        'file_path',
        'uploaded_by',
        'display_order',
        'price_type',
        'price_amount',
        'prints_available',
        'prints_url',
    ];
    
    protected bool $timestamps = true;
    
    /**
     * Get images uploaded by a specific user
     */
    public static function getByUser(int $userId): array
    {
        return static::where(['uploaded_by' => $userId]);
    }
    
    /**
     * Get recent images
     */
    public static function recent(int $limit = 12): array
    {
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table} ORDER BY created_at DESC LIMIT ?";
        return $instance->getDatabase()->fetchAll($sql, [$limit]);
    }
    
    /**
     * Get image with uploader information
     */
    public static function findWithUploader(int $id): ?array
    {
        $instance = new static();
        
        $sql = "SELECT gi.*, u.name as uploader_name 
                FROM {$instance->table} gi
                LEFT JOIN users u ON gi.uploaded_by = u.id
                WHERE gi.id = ?
                LIMIT 1";
        
        return $instance->getDatabase()->fetch($sql, [$id]);
    }
    
    /**
     * Get all images with uploader information
     */
    public static function allWithUploaders(): array
    {
        $instance = new static();
        
        $sql = "SELECT gi.*, u.name as uploader_name 
                FROM {$instance->table} gi
                LEFT JOIN users u ON gi.uploaded_by = u.id
                ORDER BY gi.display_order ASC, gi.created_at DESC";
        
        return $instance->getDatabase()->fetchAll($sql);
    }
    
    /**
     * Get paginated images with uploader information
     * 
     * @param int $page Current page number (1-based)
     * @param int $perPage Number of items per page
     * @return array ['images' => array, 'total' => int, 'page' => int, 'totalPages' => int]
     */
    public static function paginate(int $page = 1, int $perPage = 12): array
    {
        $instance = new static();
        
        // Ensure page is at least 1
        $page = max(1, $page);
        
        // Calculate offset
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$instance->table}";
        $countResult = $instance->getDatabase()->fetch($countSql);
        $total = (int) $countResult['total'];
        
        // Calculate total pages
        $totalPages = (int) ceil($total / $perPage);
        
        // Get paginated images - ordered by display_order then created_at
        $sql = "SELECT gi.*, u.name as uploader_name 
                FROM {$instance->table} gi
                LEFT JOIN users u ON gi.uploaded_by = u.id
                ORDER BY gi.display_order ASC, gi.created_at DESC
                LIMIT ? OFFSET ?";
        
        $images = $instance->getDatabase()->fetchAll($sql, [$perPage, $offset]);
        
        return [
            'images' => $images,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage
        ];
    }
    
    /**
     * Get next display order value
     */
    public static function getNextDisplayOrder(): int
    {
        $instance = new static();
        
        $sql = "SELECT COALESCE(MAX(display_order), 0) + 1 as next_order FROM {$instance->table}";
        $result = $instance->getDatabase()->fetch($sql);
        
        return (int) $result['next_order'];
    }
    
    /**
     * Swap display order with another image
     */
    public static function swapOrder(int $imageId, string $direction): bool
    {
        $instance = new static();
        $db = $instance->getDatabase();
        
        // Get current image
        $currentImage = static::find($imageId);
        if (!$currentImage) {
            return false;
        }
        
        $currentOrder = (int) $currentImage['display_order'];
        
        // Find adjacent image
        if ($direction === 'up') {
            // Find image with next lower display_order (to swap with)
            $sql = "SELECT * FROM {$instance->table} 
                    WHERE display_order < ? 
                    ORDER BY display_order DESC 
                    LIMIT 1";
        } else {
            // Find image with next higher display_order (to swap with)
            $sql = "SELECT * FROM {$instance->table} 
                    WHERE display_order > ? 
                    ORDER BY display_order ASC 
                    LIMIT 1";
        }
        
        $adjacentImage = $db->fetch($sql, [$currentOrder]);
        
        if (!$adjacentImage) {
            return false; // Already at the top/bottom
        }
        
        $adjacentOrder = (int) $adjacentImage['display_order'];
        
        // Swap the orders
        try {
            $db->beginTransaction();
            
            // Update current image
            static::update($imageId, ['display_order' => $adjacentOrder]);
            
            // Update adjacent image
            static::update((int) $adjacentImage['id'], ['display_order' => $currentOrder]);
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollback();
            return false;
        }
    }
}
