package com.example;

import java.io.BufferedInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.net.URL;
import java.nio.file.Paths;

import com.amazonaws.AmazonServiceException;
import com.amazonaws.SdkClientException;
import com.amazonaws.regions.Regions;
import com.amazonaws.services.s3.AmazonS3;
import com.amazonaws.services.s3.AmazonS3ClientBuilder;
import com.amazonaws.services.s3.model.ObjectMetadata;
import com.amazonaws.services.s3.model.PutObjectRequest;

import java.io.File;

public class S3Images {

  public static void uploadImage(String bucketName, String file_path) {
    String key_name = Paths.get(file_path).getFileName().toString();

    System.out.format("Uploading %s to S3 bucket %s...\n", file_path, bucketName);
    final AmazonS3 s3 = AmazonS3ClientBuilder.standard().withRegion(Regions.US_EAST_1).build();
    try {
      s3.putObject(bucketName, key_name, new File(file_path));
    } catch (AmazonServiceException e) {
      System.err.println(e.getErrorMessage());
      System.exit(1);
    }
  }

  public static void downloadImage(String fileName, String url_link) {

    try (BufferedInputStream in = new BufferedInputStream(new URL(url_link).openStream());
        FileOutputStream fileOutputStream = new FileOutputStream(fileName)) {
      byte dataBuffer[] = new byte[1024];
      int bytesRead;
      while ((bytesRead = in.read(dataBuffer, 0, 1024)) != -1) {
        fileOutputStream.write(dataBuffer, 0, bytesRead);
      }
    } catch (IOException e) {
      // handle exception
    }

  }
}
