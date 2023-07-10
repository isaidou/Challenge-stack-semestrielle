<?php

declare(strict_types=1);

/**
 * All Methods are for logged in users.
 */
class CommentModel extends Model
{
    /**
     * Get number of comments by user on an article
     *
     * @param string $articleId
     * @return int $rowCount
     */
    public function getUserCommentCount(string $articleId): int
    {
        $this->db->query("SELECT id FROM blog_article_comments WHERE article_id = :article_id AND user_id = :user_id");
        $this->db->bind(":article_id", $articleId);
        $this->db->bind(":user_id", $_SESSION['user_id']);

        $this->db->execute();

        return $this->db->rowCount();
    }

    /**
     * Get all parent comments inside limit
     * Check for author details and user reactions
     * Show user comments first
     */
    public function getcomments(string $articleId, int $limit = 80)
    {
        $this->db->query("SELECT blog_comments.id, blog_comments.is_edited,
                            blog_comments.content, blog_comments.created_at,
                           blog_users.pseudo, blog_users.username, blog_users.profile_img,
                            blog_users.id as user_id
                          FROM blog_article_comments as blog_comments
                          INNER JOIN blog_users ON blog_comments.user_id = blog_users.id
                          WHERE article_id = :article_id
                          ORDER BY blog_comments.created_at DESC
                          LIMIT  $limit
                         ");

        $this->db->bind(":article_id", $articleId);

        $this->db->execute();
        $rows = $this->db->fetchRows();

        return $rows;
    }



    /**
     * Get count of all comments and replies on an article
     */
    public function totalCommentsCount(string $articleId): int
    {
        $this->db->query("SELECT id FROM blog_article_comments WHERE article_id = :article_id");
        $this->db->bind(":article_id", $articleId);

        $this->db->execute();

        return $this->db->rowCount();
    }

    /**
     * Add comment to database
     * Return id if successful
     * Else return false
     */
    public function addComment(string $content, string $articleId)
    {
        $insert =  $this->db->dbInsert("blog_article_comments", [
            "content" => $content,
            "article_id" => $articleId,
            "user_id" => $_SESSION['user_id']
        ]);

        if ($insert) return $this->db->lastInsertId();
        return false;
    }

    /**
     * Check if comment belongs to user
     */
    public function isUserComment(int $commentId): bool
    {
        $this->db->query("SELECT id from blog_article_comments WHERE id = :id AND user_id = :user_id");
        $this->db->bind(":id", $commentId);
        $this->db->bind(":user_id", $_SESSION['user_id']);
        $this->db->execute();

        return $this->db->rowCount() ? true : false;
    }

    /**
     * Check if comment exists in table
     */
    public function commentExists(int $commentId): bool
    {
        $this->db->query("SELECT id from blog_article_comments WHERE id = :id");
        $this->db->bind(":id", $commentId);
        $this->db->execute();

        return $this->db->rowCount() ? true : false;
    }

    /**
     * Edit a comment
     */
    public function editComment(int $commentId, string $content)
    {
        $this->db->query("UPDATE blog_comments 
                          SET content = :content,
                            is_edited = 1,
                            created_at = :created_at
                          WHERE id = :comment_id
                        ");

        $this->db->bindMultiple([
            "content" => $content,
            "created_at" => date(DB_TIMESTAMP_FMT),
            "comment_id" => $commentId
        ]);

        return $this->db->execute();
    }

    /**f
     * Delete a comment
     */
    public function deleteComment(int $commentId): bool
    {
        $this->db->query("SELECT blog_delete_comment_aliases(:comment_id)");
        $this->db->bind(":comment_id", $commentId);

        return $this->db->execute();
    }
}
